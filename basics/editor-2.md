---
icon: pen-to-square
---

# Guide: Loan Approval Process

The loan approval process in the loan management system is designed to facilitate the review and decision-making for loan applications submitted by clients. This section provides an overview of the functionalities available on the loan approval page, which is accessible to users with the roles of **admin** and **manager**.

<figure><img src="https://gitbookio.github.io/onboarding-template-images/editor-hero.png" alt=""><figcaption></figcaption></figure>

**Accessing the Loan Approval Page**

Upon successful login, users with the admin or manager role are directed to the loan approval page. If a user does not have the appropriate permissions, they are redirected to the login page.

**Overview of Loan Applications**

On this page, users can view a list of loan applications submitted by clients. Each loan application displays relevant details, including:

* **Loan ID**: A unique identifier for the loan application.
* **Applicant Information**: Name, email, phone number, and county of the applicant.
* **Loan Details**: Requested amount, duration, purpose, date of application, and onboarding officer.
* **Collateral Information**: Details regarding the collateral offered, including name, value, and associated images.
* **Guarantor Information**: Information related to the guarantor, including their ID photos and national ID.

**Actions Available on the Loan Approval Page**

For each loan application listed, users can perform the following actions:

1. **Approve Loan**:
   * If the loan application meets all necessary criteria, the user can approve the loan. This action will:
     * Move the loan data from the `loan_applications` table to the `loan_info` table.
     * Update the status of the loan to "Active".
     * Send an approval email to the client, notifying them of the loan approval and providing instructions on how to access their portal.
     * Record the loan transaction in the accounting entries to ensure proper financial tracking.
2. **Reject Loan**:
   * If the loan application is unsatisfactory or does not meet the requirements, the user can choose to reject the application. This action will remove the application from the pending loans.
3. **Edit Application Before Approval**:
   * The user can modify the loan application details before making a final decision. By clicking this button, the user is redirected to the **Edit Loan Application** page, where they can adjust any necessary fields. This ensures that all information is accurate and satisfactory before proceeding with the approval or rejection.

**User Interface Elements**

The user interface for loan approval includes a form for submitting decisions on each loan application. Below are the buttons available within this form:

* **Reject**: Clicking this button will submit the loan ID with a request to reject the application.
* **Edit Application Before Approval**: This button allows users to navigate to the edit loan application page, where they can make necessary changes to the loan details.
* **Approve**: This button submits the loan ID with a request to approve the application.

Each button is associated with a specific action, and the form is structured to ensure that the process is user-friendly and efficient.

**Conclusion**

The loan approval process is streamlined to provide admins and managers with the necessary tools to manage loan applications effectively. With clear visibility into application details and straightforward actions for approval, rejection, or editing, users can ensure that the loan management process is both efficient and thorough.
