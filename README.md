Elizabeth School Student Attendance Management System

Overview

The Elizabeth School Student Attendance Management System is a secure web application designed to simplify and enhance the process of managing student attendance. Built with robust security mechanisms, it provides tailored functionalities for administrators, teachers, and students, ensuring streamlined operations and secure access to data.

Key Features

1. Attendance Management: Teachers can mark attendance for their classes, and students/parents can view attendance records.
2. Session and Term Management: Admins can organize school sessions and terms.
3. Report Generation: Teachers can generate daily attendance reports for their classes.
4. Secure Authentication:
   - Password-based login with email validation.
   - Multi-factor authentication (OTP) for first-time logins and registration.
5. Role-Based Access Control:
   - Admin: Manage user accounts, assign teachers and students to classes, and organize sessions and terms.
   - Teacher: Manage classroom attendance and view/generate attendance reports.
   - Student: Register and view attendance records.

Security Features

1.Password Security:
  - Passwords are hashed using MD5.
  - OTP-based password recovery mechanism.
2. Data Encryption:
  - Sensitive data is encrypted using AES-256-CBC.
  - Encrypted transmission over HTTPS with an SSL certificate from ZeroSSL.
3. Session Management:
  - Automatic session timeout after 30 minutes of inactivity.
  - Maximum login attempts set to 3, after which the account is locked.
4. Role-Based Access Control:
  - Strictly enforces permissions based on user roles.

Technologies Used

Backend: PHP, MySQL
Frontend: HTML, CSS, JavaScript
Security: MD5 for password hashing, AES-256-CBC for encryption, HTTPS with SSL.
Hosting: Linux server with ZeroSSL certificate.

System Functionalities

1. Administrator:
  - Manage user accounts.
  - Assign teachers to classes and students to teachers.
  - Organize sessions and terms.
2. Teacher:
  - Mark and view attendance for assigned classes.
  - Generate attendance reports.
3. Student:
  - Self-register on the platform.
  - View attendance records sorted by term and session.
