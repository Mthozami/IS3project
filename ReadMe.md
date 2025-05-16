## This is the Information Systems 3 Assignment 3


### Brief Overview of the Assignment
We are a group of six final-year students who collaborated to develop a Library Management System aimed at small to medium-scale enterprises. The system is designed to offer free access to books within the community under specific terms and conditions. Users are required to visit the library so that they can get registered in-person by a librarian, and after they got registered, they can log into the system to view their borrowing history, Fines history, Payment history and update their personal information. Each user may borrow only one book at a time for a period of seven days. Failure to return the book on time results in a fine of R25 per day. In cases of a lost or damaged book, the user will be responsible for an additional fee of R600 as a replacement of that book. If a book is reported as lost on the due date, only the R600 fee will be applied without the daily fine. Once the fine is paid, users will be able to view his payment history where we are issuing a receipt number which serve as proof of payment among the details in the payments history of that particular user. 



### Objective
Objective of the project
The objective of this project is to design and implement a fully functional database that is accessible through the web browser using MySQL. The main aim is to showcase the practical application of key database concepts including database design (conceptual, physical, and logical), client-server architecture, normalization (1NF, 2NF, 3NF), transaction processing, concurrency control, data integrity enforcement, database security, backup and recovery management, and the prevention of deadlocks 

### Technologies used
•	Frontend: HTML, CSS, JavaScript
•	Backend: PHP
•	Database: MySQL
•	Version Control: Git and Github

## STEPS INVOLVED IN THE DEVELOPMENT OF THE PROJECT

#### Planning & Requirements Gathering

• Defined the purpose of the system: a Library Management System for book borrowing and fine tracking.

• Identified key system actors: Admin and Borrower.

#### Database Design

• Created an Entity-Relationship Diagram (ERD) to model how Users, Books, Borrowings, Fines, and Transactions interact.

• Normalized the database up to 3NF to ensure data consistency and eliminate redundancy.

#### Logical & Physical Implementation

• Translated ERD into actual MySQL tables with appropriate fields, data types, primary and foreign keys.

• Indexed key columns for performance.

#### Backend Development with PHP

• Used prepared statements and session handling for security and authentication.

• Implemented full CRUD operations for users, books, and borrowings.

• Ensured fine generation, payment handling, and transaction recording.

#### Frontend Development

• Built user interfaces using HTML, CSS, and JavaScript.

• Used alerts and confirmations to enhance user experience.

• Created separate dashboards for Admin and Borrower roles.

#### Security & Access Control

• Applied session-based authentication.

• Restricted access to certain pages based on user roles.

#### Backup & Recovery

• Developed a batch script to automate database backups daily using mysqldump.

• Documented how to restore the database using the .sql file.

#### Testing & Debugging

• Simulated multiple users to test concurrency and transaction safety.

• Ensured proper error handling and validation throughout.