# MediCare Plus Web Application Report

## Project Overview
MediCare Plus is a dynamic PHP + SQLite web application designed for three user groups: Admin, Doctor, and Patient. The system digitizes core healthcare workflows including profile discovery, appointment scheduling, report sharing, secure messaging, and patient feedback.

## Assumptions
- SQLite is used for local deployment and can be migrated to MySQL with minor SQL syntax changes.
- One hospital network with multiple service departments.
- Patients can self-register; admin and doctors are seeded initially.
- File downloads for reports are handled via secure links (URL/path placeholder).

---

## Task 01(a): Similar Systems Analysis

| Platform | Strengths | Weaknesses | Adopted Design Decision |
|---|---|---|---|
| Practo | Rich doctor search/filter and ratings | Dense UI on some screens | Added focused filter by specialization/location + rating summary |
| eChannelling | Strong appointment flow | Limited personalized dashboarding | Added role-oriented views for patient/doctor/admin |
| MyChart | Powerful health record access | Complex learning curve for first-time users | Simplified report table + minimal nav hierarchy |

### Key Design Factors Considered
1. **Accessibility & Clarity**: High-contrast futuristic visual theme with clear text hierarchy.
2. **Workflow Speed**: Direct navigation to core healthcare actions.
3. **Trust & Transparency**: Doctor qualifications, fees, and ratings visible upfront.
4. **Security-by-Design**: Session-based authentication with role checks.

---

## Task 01(b): UI Design + Sitemap + Justifications

## UI Mockup Notes (Low-Fidelity)
1. **Home page**: Hero + three feature cards + demo credential section.
2. **Doctors page**: Top filter bar + card grid showing profile metadata.
3. **Appointments page**: Booking form + role-aware appointment table with status update.
4. **Reports page**: Doctor upload form + patient-accessible report listing/download.
5. **Messages page**: Secure compose panel + historical conversation list.

### Sitemap
```text
Home
├── Doctors
├── Services
├── Login
├── Register
└── Authenticated Area
    ├── Appointments
    ├── Reports
    ├── Messages
    ├── Feedback
    └── Admin Dashboard (Admin only)
```

### Design Justifications
- **Card-based information architecture** improves scannability and mobile responsiveness.
- **Gradient + glassmorphism** creates a futuristic identity suitable for digital healthcare branding.
- **Animation on load** offers modern UX while keeping CPU overhead low.
- **Unified component styling** ensures consistency and easier maintenance.

---

## Task 02(a): Front-end Implementation (L02)

### Technologies
- HTML5 for semantic layout
- CSS3 for responsive/futuristic styling and animation
- Vanilla JS for entrance animations

### User-Friendly Interface Features
- Responsive grid layouts
- Consistent form controls and validation hints
- Highlighted status badges and alert panels
- Search/filter inputs for doctors and services

---

## Task 02(b): Back-end Functionality (L03)

### Implemented Modules
1. **Authentication/Authorization**
   - Secure login with password hashing
   - Session management
   - Role-based access control (`admin`, `doctor`, `patient`)

2. **Doctor Profiles**
   - Stores specialization, experience, qualifications, fees, availability, location
   - Live average rating aggregation

3. **Service Listings**
   - Categorized services with details and filters

4. **Appointment Booking System**
   - Patient booking form
   - Role-based appointment listing
   - Status updates by doctor/admin

5. **Patient Registration & History Access**
   - Self-registration for patients
   - Access to appointment history and reports

6. **Medical Reports Access**
   - Doctors/admin publish report entries
   - Patients can view report summaries and open download links

7. **Secure Messaging**
   - Internal message exchange among users

8. **Feedback & Ratings**
   - Patients submit doctor ratings (1–5) and comments

9. **Error Handling**
   - Input validation checks
   - User-friendly error/success alerts

---

## Task 03: Testing and Evaluation (L04)

## Testing Overview
A mix of static checks, role-flow checks, and functional checks were planned for reliability.

## Test Plan
| ID | Scenario | Type | Expected Result |
|---|---|---|---|
| T01 | User login with valid credentials | Functional | Redirect to home with active session |
| T02 | Invalid login | Negative | Error alert shown |
| T03 | Patient registration with duplicate email | Negative | Registration error alert |
| T04 | Doctor filter by specialization | Functional | Matching doctor cards displayed |
| T05 | Patient appointment booking | Functional | New appointment record with Pending status |
| T06 | Doctor updates appointment status | Functional | Status persists as selected |
| T07 | Patient report access | Functional | Only own reports visible |
| T08 | Unauthorized admin page access | Security | 403 access denied |
| T09 | Message send/retrieval | Functional | Message appears in conversation history |
| T10 | Feedback rating out of range | Validation | Input blocked/error shown |

## Feedback Evaluation
- **Positive outcomes**: Fast navigation, visually modern interface, straightforward workflows.
- **Potential improvements**: Real-time notifications, calendar slot locking, and payment integration.

---

## Web System Documentation

## File Structure
```text
/config/db.php           -> DB connection, schema creation, seed data
/includes/auth.php       -> Auth helper and role guards
/includes/header.php     -> Shared navigation and layout start
/includes/footer.php     -> Shared scripts and layout end
/assets/css/style.css    -> Futuristic styling and animation visuals
/assets/js/app.js        -> UI entrance animation
*.php                    -> Feature pages (auth, doctors, services, appointments, etc.)
/docs/report.md          -> Assessment report
```

## Setup Instructions
1. Install PHP (8.0+ recommended) with SQLite extension.
2. Navigate to project root.
3. Start server:
   ```bash
   php -S 0.0.0.0:8000
   ```
4. Open `http://localhost:8000`.
5. Login with seeded demo users (`Password@123`).

## Security Notes
- Passwords are stored using `password_hash`.
- Role checks guard protected pages.
- Output escapes use `htmlspecialchars` to reduce XSS risk.

## Future Enhancements
- Online consultation fee payment gateway integration
- Health blogs and wellness tips CMS
- Real medical file upload/download with access tokens
- Email/SMS reminders for appointments
