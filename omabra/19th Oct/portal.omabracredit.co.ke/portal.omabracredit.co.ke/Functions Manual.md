To improve the `getLoanDetails($loan_id)` function, we'll focus on organizing the variables based on your categories. I'll propose how each group can be structured, returning clear, individual variables instead of arrays. Here's an updated approach:

### 1. **$loanCalculations** (Core loan-related calculations)
These will be calculated and returned directly as variables:
- `$loanBalance`: Outstanding balance of the loan.
- `$equalInstallments`: Equal installment amount based on loan terms.
- `$accruedInterest`: Interest accrued until today.
- `$nextPaymentDate`: Date of the next payment due.
- `$nextPaymentAmount`: The amount due for the next payment.
- `$periodsPassed`: Number of periods (weeks/months) that have passed since the loan started.
- `$periodsRemaining`: Number of periods remaining until the loan matures.
- `$clearToday`: Amount required to fully clear the loan today.
- `$clearedStatus`: Boolean or status indicator if the loan is fully paid or not (this could be a separate function, e.g., `isLoanCleared($loan_id)`).

### 2. **$loanBusinessReports** (Reports for business analysis)
These values are usually aggregations or summary reports across multiple loans:
- `$allAccruedInterest`: Total interest accrued across selected loans.
- `$payments`: Total sum of all payments made towards the loan.
- `$loansDisbursed`: Total amount disbursed for loans within a given period/criteria.
- `$countLoans`: Number of loans processed or selected.
- `$countClients`: Number of unique clients with active loans.
- `$stageStatus`: Current stage of the loan from the `loan_stage` table.
- `$loanBookStatus`: The status of the loan in the `loan_info` table (e.g., Active, Cleared, Rejected).

### 3. **$loanIdentification** (Basic loan identification details)
These details will be fetched and returned as variables:
- `$clientName`: Client's name.
- `$phoneNumber`: Client's phone number.
- `$loanPrincipal`: Principal amount of the loan.
- `$loanInterestRate`: The interest rate applied to the loan.
- `$loanStartDate`: The start date of the loan.
- `$loanEndDate`: Date when the last payment is due.

### 4. **$loan_details_info** (Direct details from `loan_info`)
Return specific details directly from the `loan_info` table:
- `$national_id`: Client’s national ID.
- `$loan_purpose`: Purpose of the loan.
- `$duration`: Duration of the loan.
- `$date_applied`: Date the loan was applied.
- `$interest_rate_period`: Frequency of interest application (Week/Month).
- `$duration_period`: Duration period type (Week/Month).

### 5. **$loanSecurityInfo** (Loan security and collateral information)
Details about collateral and guarantors will be returned as:
- `$collateralName`: Name of the collateral.
- `$collateralValue`: Value of the collateral.
- `$collateralPic1`: Path or URL to the first collateral picture.
- `$collateralPic2`: Path or URL to the second collateral picture.
- `$guarantor1Name`: Name of the first guarantor.
- `$guarantor1Phone`: Phone number of the first guarantor.
- `$guarantor2Name`: Name of the second guarantor.
- `$guarantor2Phone`: Phone number of the second guarantor.
- `$onboardingOfficer`: Name or email of the officer who onboarded the loan.

---

### Function Structure

```php
function getLoanDetails($loan_id) {
    // Core Calculations
    $loanBalance = calculateLoanBalance($loan_id); // implement logic
    $equalInstallments = calculateInstallments($loan_id); // implement logic
    $accruedInterest = calculateAccruedInterest($loan_id); // implement logic
    $nextPaymentDate = getNextPaymentDate($loan_id); // implement logic
    $nextPaymentAmount = getNextPaymentAmount($loan_id); // implement logic
    $periodsPassed = getPeriodsPassed($loan_id); // implement logic
    $periodsRemaining = getPeriodsRemaining($loan_id); // implement logic
    $clearToday = calculateClearTodayAmount($loan_id); // implement logic
    $clearedStatus = isLoanCleared($loan_id); // implement logic

    // Business Reports
    $allAccruedInterest = getAllAccruedInterest($loan_id); // implement logic
    $payments = getTotalPayments($loan_id); // implement logic
    $loansDisbursed = getLoansDisbursed($loan_id); // implement logic
    $countLoans = countLoans(); // implement logic
    $countClients = countClients(); // implement logic
    $stageStatus = getStageStatus($loan_id); // implement logic
    $loanBookStatus = getLoanBookStatus($loan_id); // implement logic

    // Identification
    $clientName = getClientName($loan_id); // implement logic
    $phoneNumber = getClientPhoneNumber($loan_id); // implement logic
    $loanPrincipal = getLoanPrincipal($loan_id); // implement logic
    $loanInterestRate = getLoanInterestRate($loan_id); // implement logic
    $loanStartDate = getLoanStartDate($loan_id); // implement logic
    $loanEndDate = getLoanEndDate($loan_id); // implement logic

    // Loan Info
    $national_id = getNationalID($loan_id); // implement logic
    $loan_purpose = getLoanPurpose($loan_id); // implement logic
    $duration = getLoanDuration($loan_id); // implement logic
    $date_applied = getDateApplied($loan_id); // implement logic
    $interest_rate_period = getInterestRatePeriod($loan_id); // implement logic
    $duration_period = getDurationPeriod($loan_id); // implement logic

    // Loan Security Info
    $collateralName = getCollateralName($loan_id); // implement logic
    $collateralValue = getCollateralValue($loan_id); // implement logic
    $collateralPic1 = getCollateralPic1($loan_id); // implement logic
    $collateralPic2 = getCollateralPic2($loan_id); // implement logic
    $guarantor1Name = getGuarantor1Name($loan_id); // implement logic
    $guarantor1Phone = getGuarantor1Phone($loan_id); // implement logic
    $guarantor2Name = getGuarantor2Name($loan_id); // implement logic
    $guarantor2Phone = getGuarantor2Phone($loan_id); // implement logic
    $onboardingOfficer = getOnboardingOfficer($loan_id); // implement logic

    return compact(
        'loanBalance', 'equalInstallments', 'accruedInterest', 'nextPaymentDate', 
        'nextPaymentAmount', 'periodsPassed', 'periodsRemaining', 'clearToday', 
        'clearedStatus', 'allAccruedInterest', 'payments', 'loansDisbursed', 'countLoans', 
        'countClients', 'stageStatus', 'loanBookStatus', 'clientName', 'phoneNumber', 
        'loanPrincipal', 'loanInterestRate', 'loanStartDate', 'loanEndDate', 
        'national_id', 'loan_purpose', 'duration', 'date_applied', 'interest_rate_period', 
        'duration_period', 'collateralName', 'collateralValue', 'collateralPic1', 
        'collateralPic2', 'guarantor1Name', 'guarantor1Phone', 'guarantor2Name', 
        'guarantor2Phone', 'onboardingOfficer'
    );
}
```

This structure categorizes everything as individual variables, making it cleaner, more organized, and easier to manage. Let me know your thoughts on this approach!


***Core Calculations***

To handle the scenario where partial payments are made and the status remains 'not paid,' your approach of calculating the loan balance and other metrics using direct comparisons between `payments` and `expected_payments` is solid. By avoiding the 'paid'/'not paid' statuses, you ensure that the full picture of the loan is reflected in the balance and period calculations.

Here’s how you could refine the core calculation functions based on this approach:

1. **$loanBalance**:
   - Fetch the total amount expected from `expected_payments` and subtract the total payments from the `payments` table for that loan.

2. **$equalInstallments**:
   - This could be calculated by dividing the total expected amount by the number of periods (rows in `expected_payments`).

3. **$accruedInterest**:
   - Sum the `interest_income` values from `expected_payments` where the `payment_status` is 'not paid' or consider accrued interest based on periods passed.

4. **$nextPaymentDate**:
   - Fetch the earliest `payment_date` from `expected_payments` where `payment_status` is 'not paid'.

5. **$nextPaymentAmount**:
   - Fetch the `installment_amount` for the next unpaid payment from `expected_payments`.

6. **$periodsPassed**:
   - Count the number of periods where `payment_date` has passed, or compare today's date to the `payment_date`.

7. **$periodsRemaining**:
   - Subtract the periods passed from the total number of periods in `expected_payments`.

8. **$clearToday**:
   - Calculate the total unpaid principal from `expected_payments` for all future installments, then subtract any payments already made.

9. **$clearedStatus**:
   - Check if the total payments in `payments` exceed or equal the total expected payments from `expected_payments` for that loan.

Would you like to implement any of these calculations next?


***Business Reports***

Breakdown of Functions:
getAllAccruedInterest($loan_id):

Calculates the total interest accrued up to today for a specific loan based on entries in the expected_payments table.
getTotalPayments($loan_id):

Retrieves the sum of all payments made for a specific loan from the payments table.
getLoansDisbursed($loan_id):

Fetches the loan principal (i.e., amount disbursed) from the loan_info table.
countLoans():

Counts the total number of loans in the system by querying the loan_info table.
countClients():

Counts the total number of unique clients who have taken loans, based on distinct client IDs in the loan_info table.
getStageStatus($loan_id):

Fetches the current stage status of a specific loan from the loan_info table. The result is a string such as "disbursed," "active," or "completed."
getLoanBookStatus():

Calculates the total outstanding balance across all loans by subtracting total payments from the loan principal for each loan.

```
$loan_id = 123; // Example loan ID

```

// Fetching business report data
$allAccruedInterest = getAllAccruedInterest($loan_id);
$payments = getTotalPayments($loan_id);
$loansDisbursed = getLoansDisbursed($loan_id);
$countLoans = countLoans();
$countClients = countClients();
$stageStatus = getStageStatus($loan_id);
$loanBookStatus = getLoanBookStatus();

// Output or use the data as needed
echo "Accrued Interest: $allAccruedInterest\n";
echo "Total Payments: $payments\n";
echo "Loan Disbursed: $loansDisbursed\n";
echo "Total Loans: $countLoans\n";
echo "Total Clients: $countClients\n";
echo "Stage Status: $stageStatus\n";
echo "Loan Book Status (Outstanding Balance): $loanBookStatus\n";

```


if $loan_id is array:

Yes, you can modify the functions to handle an array of loan IDs. This way, you can retrieve data for multiple loans at once. You’d generally adjust the SQL queries to use `IN` clauses and return results accordingly. 

### Example Modifications for Array of Loan IDs:

1. **`getAllAccruedInterest`**:
   ```php
   function getAllAccruedInterest(array $loan_ids) {
       global $conn;
       $placeholders = implode(',', array_fill(0, count($loan_ids), '?'));
       $sql = "SELECT loan_id, SUM(interest_amount) AS total_accrued_interest
               FROM expected_payments
               WHERE loan_id IN ($placeholders) AND due_date <= CURDATE()
               GROUP BY loan_id";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param(str_repeat('i', count($loan_ids)), ...$loan_ids);
       $stmt->execute();
       $result = $stmt->get_result();
       $accrued_interest = [];
       while ($row = $result->fetch_assoc()) {
           $accrued_interest[$row['loan_id']] = $row['total_accrued_interest'] ?: 0;
       }
       return $accrued_interest;
   }
   ```

2. **`getTotalPayments`**:
   ```php
   function getTotalPayments(array $loan_ids) {
       global $conn;
       $placeholders = implode(',', array_fill(0, count($loan_ids), '?'));
       $sql = "SELECT loan_id, SUM(payment_amount) AS total_payments
               FROM payments
               WHERE loan_id IN ($placeholders)
               GROUP BY loan_id";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param(str_repeat('i', count($loan_ids)), ...$loan_ids);
       $stmt->execute();
       $result = $stmt->get_result();
       $payments = [];
       while ($row = $result->fetch_assoc()) {
           $payments[$row['loan_id']] = $row['total_payments'] ?: 0;
       }
       return $payments;
   }
   ```

3. **For Other Functions**:
   You can follow a similar approach for the other functions, making sure to adjust the SQL to handle arrays of loan IDs.

### Example Usage:
```php
$loan_ids = [123, 456, 789]; // Example array of loan IDs

// Fetching business report data for multiple loans
$allAccruedInterest = getAllAccruedInterest($loan_ids);
$payments = getTotalPayments($loan_ids);
// ...and so on for other functions

// Output or use the data as needed
foreach ($loan_ids as $loan_id) {
    echo "Loan ID: $loan_id\n";
    echo "Accrued Interest: {$allAccruedInterest[$loan_id]}\n";
    echo "Total Payments: {$payments[$loan_id]}\n";
    // Add other outputs as needed
}
```

### Notes:
- Ensure you handle the returned data correctly as associative arrays where keys are the loan IDs.
- This modification allows you to efficiently retrieve data for multiple loans in a single query, improving performance.


**************************************************************************************************

Here's how you can implement the logic for the `$loanIdentification` set, which includes basic identification details for each loan. Each function will fetch the relevant data from the `loan_info` table based on the loan ID(s) provided.

### Loan Identification Functions

1. **`getClientName`**:
   ```php
   function getClientName($loan_id) {
       global $conn;
       $stmt = $conn->prepare("SELECT client_name FROM loan_info WHERE loan_id = ?");
       $stmt->bind_param("s", $loan_id);
       $stmt->execute();
       $result = $stmt->get_result();
       return $result->fetch_assoc()['client_name'] ?? null;
   }
   ```

2. **`getPhoneNumber`**:
   ```php
   function getPhoneNumber($loan_id) {
       global $conn;
       $stmt = $conn->prepare("SELECT phone_number FROM loan_info WHERE loan_id = ?");
       $stmt->bind_param("s", $loan_id);
       $stmt->execute();
       $result = $stmt->get_result();
       return $result->fetch_assoc()['phone_number'] ?? null;
   }
   ```

3. **`getLoanPrincipal`**:
   ```php
   function getLoanPrincipal($loan_id) {
       global $conn;
       $stmt = $conn->prepare("SELECT requested_amount FROM loan_info WHERE loan_id = ?");
       $stmt->bind_param("s", $loan_id);
       $stmt->execute();
       $result = $stmt->get_result();
       return $result->fetch_assoc()['requested_amount'] ?? null;
   }
   ```

4. **`getLoanInterestRate`**:
   ```php
   function getLoanInterestRate($loan_id) {
       global $conn;
       $stmt = $conn->prepare("SELECT interest_rate FROM loan_info WHERE loan_id = ?");
       $stmt->bind_param("s", $loan_id);
       $stmt->execute();
       $result = $stmt->get_result();
       return $result->fetch_assoc()['interest_rate'] ?? null;
   }
   ```

5. **`getLoanStartDate`**:
   ```php
   function getLoanStartDate($loan_id) {
       global $conn;
       $stmt = $conn->prepare("SELECT date_applied FROM loan_info WHERE loan_id = ?");
       $stmt->bind_param("s", $loan_id);
       $stmt->execute();
       $result = $stmt->get_result();
       return $result->fetch_assoc()['date_applied'] ?? null;
   }
   ```

6. **`getLoanEndDate`**:
   ```php
   function getLoanEndDate($loan_id) {
       global $conn;
       // Assuming end date is calculated based on start date and duration
       $stmt = $conn->prepare("SELECT DATE_ADD(date_applied, INTERVAL duration DAY) AS loan_end_date FROM loan_info WHERE loan_id = ?");
       $stmt->bind_param("s", $loan_id);
       $stmt->execute();
       $result = $stmt->get_result();
       return $result->fetch_assoc()['loan_end_date'] ?? null;
   }
   ```

### Usage Example
You can call these functions to populate your `$loanIdentification` set for a given loan ID:

```php
$loan_id = 'ln000003'; // Example loan ID

$clientName = getClientName($loan_id);
$phoneNumber = getPhoneNumber($loan_id);
$loanPrincipal = getLoanPrincipal($loan_id);
$loanInterestRate = getLoanInterestRate($loan_id);
$loanStartDate = getLoanStartDate($loan_id);
$loanEndDate = getLoanEndDate($loan_id);

// Output or use the data as needed
echo "Client Name: $clientName\n";
echo "Phone Number: $phoneNumber\n";
echo "Loan Principal: $loanPrincipal\n";
echo "Interest Rate: $loanInterestRate%\n";
echo "Loan Start Date: $loanStartDate\n";
echo "Loan End Date: $loanEndDate\n";
```

### Notes:
- Each function fetches a specific detail about the loan based on the provided loan ID.
- Make sure to handle cases where a loan might not be found, returning `null` or a default value if necessary.
- You can adjust the SQL queries as needed to fetch additional information or modify the logic based on your application’s requirements.

```php

// Loan Identification
$clientName = $loan_info['client_id']; // Assuming you have a way to get client name from client ID
$phoneNumber = $loan_info['phone_number'];
$loanPrincipal = $loan_info['requested_amount'];
$loanInterestRate = $loan_info['interest_rate'];
$loanStartDate = $loan_info['created_at']; // Assuming this is the activation date
$loanEndDate = calculateLoanEndDate($loanStartDate, $loan_info['duration'], $loan_info['duration_period']); // Implement this function as needed

```

