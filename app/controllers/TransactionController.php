<?php
/**
 * =====================================================================
 * MPVS — Transaction controller (SRS 4.2.3 / 4.2.4 / 4.2.5)
 * ---------------------------------------------------------------------
 * MANUAL VERIFICATION WORKFLOW:
 *   1. Administrator records a transaction (Add Transaction) -> Pending
 *   2. Redirected to Verify Transaction, which searches the new record
 *   3. Administrator clicks "Verify Payment" or "Mark as Failed"
 *   4. Database is updated; reports and dashboard statistics follow.
 * No external M-Pesa API is used.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Receipt;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /* -----------------------------------------------------------------
     * Add Transaction (administrator only)
     * ----------------------------------------------------------------- */

    /** GET /transactions/create — record a new transaction. */
    public function createForm(array $params = []): void
    {
        Auth::requireRole('admin');
        $this->render('transactions/create', [
            'title' => 'Add Transaction',
            'user'  => Auth::user(),
        ]);
    }

    /** POST /transactions/store — save the transaction as Pending. */
    public function store(array $params = []): void
    {
        Auth::requireRole('admin');
        $this->verifyCsrf();

        $code  = strtoupper($this->post('mpesa_code'));
        $name  = trim($this->post('sender_name'));
        $phone = trim($this->post('sender_phone'));
        $amount = $this->post('amount');

        $errors = [];
        if ($code !== '' && preg_match('/^[A-Z0-9]{10}$/', $code) !== 1) {
            $errors['mpesa_code'] = 'Transaction code must be 10 alphanumeric characters (e.g. QHJ7K8L9MN).';
        } elseif ($code !== '' && Transaction::findByCode($code) !== null) {
            $errors['mpesa_code'] = 'This transaction code already exists in the system. Codes must be unique.';
        }
        if ($name === '' || strlen($name) < 2) {
            $errors['sender_name'] = 'Sender name is required (at least 2 characters).';
        }
        if ($phone === '') {
            $errors['sender_phone'] = 'Sender phone number is required.';
        } elseif (preg_match('/^\+?[0-9\s\-]{9,15}$/', $phone) !== 1) {
            $errors['sender_phone'] = 'Enter a valid phone number (e.g. 0712345678).';
        }
        if ($amount === '' || !is_numeric($amount) || (float) $amount <= 0) {
            $errors['amount'] = 'Amount must be a positive number.';
        } elseif ((float) $amount > 999999999.99) {
            $errors['amount'] = 'Amount is too large.';
        }

        if (!empty($errors)) {
            remember_inputs([
                'mpesa_code'   => $code,
                'sender_name'  => $name,
                'sender_phone' => $phone,
                'amount'       => $amount,
            ]);
            Flash::set('danger', 'Please correct the highlighted fields.');
            $this->render('transactions/create', [
                'title' => 'Add Transaction',
                'user'  => Auth::user(),
                'errors' => $errors,
                'old' => $_SESSION['old_input'] ?? [],
            ]);
            return;
        }

        // Auto-generate a unique code when the field was left blank.
        if ($code === '') {
            $code = Transaction::generateCode();
        }

        // Keep the customer registry in sync (used by reports & receipts).
        $customerId = Customer::findOrCreate($phone, $name);

        $transactionId = Transaction::create([
            'mpesa_code'  => $code,
            'customer_id' => $customerId,
            'phone'       => $phone,
            'amount'      => (float) $amount,
            'status'      => 'pending',
        ]);

        AuditLog::log(Auth::id(), 'transaction_created', 'Recorded transaction ' . $code . ' — ' . money((float) $amount) . ' from ' . $name);
        Flash::set('success', 'Transaction ' . $code . ' recorded as Pending. Review it below to verify the payment.');

        // Auto-search the newly created transaction on the Verify page.
        redirect('verify?code=' . urlencode($code));
    }

    /* -----------------------------------------------------------------
     * Verify Transaction page
     * ----------------------------------------------------------------- */

    /** GET /verify — search a locally recorded transaction & review it. */
    public function verifyForm(array $params = []): void
    {
        Auth::requireLogin();

        $code = strtoupper($this->input('code'));
        $transaction = null;
        $notFound = false;

        if ($code !== '') {
            $found = Transaction::findByCode($code);
            if ($found === null) {
                $notFound = true;
            } else {
                $transaction = Transaction::find((int) $found['id']);
            }
        }

        $this->render('transactions/verify', [
            'title'       => 'Verify Transaction',
            'user'        => Auth::user(),
            'code'        => $code,
            'transaction' => $transaction,
            'notFound'    => $notFound,
            'isAdmin'     => Auth::isAdmin(),
        ]);
    }

    /** POST /transactions/verify — mark a pending transaction Verified. */
    public function markVerified(array $params = []): void
    {
        Auth::requireRole('admin');
        $this->verifyCsrf();

        $id = (int) $this->post('id');
        $t = Transaction::find($id);
        if ($t === null) {
            Flash::set('danger', 'Transaction not found.');
            redirect('verify');
        }
        if ($t['status'] !== 'pending') {
            Flash::set('warning', 'Only pending transactions can be verified. This transaction is already ' . $t['status'] . '.');
            redirect('transactions/show/' . $id);
        }

        Transaction::markVerified($id, Auth::id());
        Receipt::create($id, Auth::id());
        AuditLog::log(Auth::id(), 'verify_transaction', 'Verified transaction ' . $t['mpesa_code'] . ' — ' . money((float) $t['amount']));

        Flash::set('success', 'Payment ' . $t['mpesa_code'] . ' verified successfully! ' . money((float) $t['amount'])
            . ' received from ' . ($t['customer_name'] ?? $t['phone']) . '. A receipt has been generated.');
        redirect('receipt/' . $id);
    }

    /** POST /transactions/fail — mark a pending transaction Failed. */
    public function markFailed(array $params = []): void
    {
        Auth::requireRole('admin');
        $this->verifyCsrf();

        $id = (int) $this->post('id');
        $t = Transaction::find($id);
        if ($t === null) {
            Flash::set('danger', 'Transaction not found.');
            redirect('verify');
        }
        if ($t['status'] !== 'pending') {
            Flash::set('warning', 'Only pending transactions can be marked as failed. This transaction is already ' . $t['status'] . '.');
            redirect('transactions/show/' . $id);
        }

        Transaction::markFailed($id, Auth::id());
        AuditLog::log(Auth::id(), 'transaction_failed', 'Marked transaction ' . $t['mpesa_code'] . ' as failed');

        Flash::set('warning', 'Transaction ' . $t['mpesa_code'] . ' has been marked as failed.');
        redirect('transactions/show/' . $id);
    }

    /* -----------------------------------------------------------------
     * Transaction records
     * ----------------------------------------------------------------- */

    /** GET /transactions — list, search, filter and paginate records. */
    public function index(array $params = []): void
    {
        Auth::requireLogin();

        $filters = [
            'code'      => $this->input('code'),
            'phone'     => $this->input('phone'),
            'customer'  => $this->input('customer'),
            'status'    => $this->input('status'),
            'verifier'  => $this->input('verifier'),
            'date_from' => $this->input('date_from'),
            'date_to'   => $this->input('date_to'),
        ];
        $page = max(1, (int) $this->input('page', '1'));
        $result = Transaction::search($filters, $page, 15);

        $this->render('transactions/index', [
            'title'   => 'Transaction Records',
            'user'    => Auth::user(),
            'rows'    => $result['rows'],
            'total'   => $result['total'],
            'pages'   => $result['pages'],
            'page'    => $result['page'],
            'filters' => $filters,
            'verifiers' => Transaction::verifiers(),
        ]);
    }

    /** GET /transactions/show/{id} — transaction detail. */
    public function show(array $params = []): void
    {
        Auth::requireLogin();
        $id = (int) ($params['id'] ?? 0);
        $transaction = Transaction::find($id);
        if ($transaction === null) {
            $this->render('errors/404', [], 'none');
            return;
        }
        $receipt = Receipt::findByTransactionId($id);

        $this->render('transactions/show', [
            'title'       => 'Transaction Details',
            'user'        => Auth::user(),
            'transaction' => $transaction,
            'receipt'     => $receipt,
            'isAdmin'     => Auth::isAdmin(),
        ]);
    }

    /** GET /receipt/{id} — printable digital receipt. */
    public function receipt(array $params = []): void
    {
        Auth::requireLogin();
        $transactionId = (int) ($params['id'] ?? 0);

        $receipt = Receipt::findByTransactionId($transactionId);
        if ($receipt === null) {
            // Some transactions (e.g. pending) never received a receipt.
            $transaction = Transaction::find($transactionId);
            if ($transaction === null) {
                $this->render('errors/404', [], 'none');
                return;
            }
            Flash::set('warning', 'No receipt available — this transaction was not verified.');
            redirect('transactions/show/' . $transactionId);
        }

        $this->render('receipts/show', [
            'title'   => 'Payment Receipt',
            'user'    => Auth::user(),
            'receipt' => $receipt,
        ]);
    }
}
