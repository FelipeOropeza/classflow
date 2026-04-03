# ClassFlow ERP Implementation Plan

## Overview
ClassFlow is an integrated school management system (ERP) designed to digitize pedagogical and administrative processes for a single school unit. The focus is on providing a seamless experience for Directors, Teachers, and Guardians.

- **Objective:** Replace manual class diaries and grade sheets with a real-time digital platform.
- **Target Audience:** School Directors (Admin), Teachers (Pedagogical), and Guardians (Monitoring).

---

## Project Type: WEB
**Primary Agent:** `frontend-specialist` (for UI/Dashboard)
**Supporting Agents:** `backend-specialist` (API/Logic), `database-architect` (Schema)

---

## Success Criteria
1.  **Authentication:** RBAC (Role-Based Access Control) for Director, Teacher, and Guardian.
2.  **Structure:** Ability to create Academic Years, Terms, Subjects, and Classes.
3.  **Pedagogical Flow:** 
    - Teachers can record daily attendance and grades.
    - Automatic calculation of averages and attendance rates.
4.  **Guardian Access:** Real-time visualization of student performance and attendance.
5.  **Performance:** Page transitions < 300ms (leveraging Inertia.js).

---

## Tech Stack
- **Backend:** Laravel 13 (PHP 8.3+)
- **Frontend:** Vue 3 (Composition API) + Inertia.js
- **Styling:** Tailwind CSS v4
- **Database:** MySQL (local)
- **Icons:** Lucide Vue Next
- **State Management:** Core Inertia + Pinia (if needed for complex UI state)

---

## File Structure
```text
app/
├── Models/              # Year, Term, Class, Subject, Student, Enrollment, Grade, Attendance
├── Http/
│   ├── Controllers/    # Director, Teacher, Guardian specific controllers
│   └── Middleware/     # Role-based protection
database/
├── migrations/         # All schema definitions
resources/
├── js/
│   ├── Pages/          # Dashboards and CRUDs
│   ├── Components/     # Reusable UI elements (Shadcn-style)
│   └── Layouts/        # Authenticated Layouts
routes/
├── web.php             # Web & Inertia routes
```

---

## Task Breakdown

### Phase 1: Foundation (P0)
| Task ID | Name | Agent | Description | Verify |
|---------|------|-------|-------------|--------|
| F1 | DB Schema Design | `database-architect` | Create migrations for Years, Terms, Classes, Subjects, Enrollments, Grades, and Attendance. | [x] |
| F2 | RBAC Setup | `backend-specialist` | Implement Roles (Director, Teacher, Guardian) using a simple Role column. | [x] |
| F3 | Base Layouts | `frontend-specialist` | Create the Sidebar/Navbar layout with Tailwind 4 and Lucide icons. | Visual check in browser. |

### Phase 2: Core Admin (Director) (P1)
| Task ID | Name | Agent | Description | Verify |
|---------|------|-------|-------------|--------|
| A1 | Academic Setup | `backend-specialist` | CRUD for Years, Terms, and Subjects. | Form submission & list view. |
| A2 | Class Management | `backend-specialist` | Create Classes and assign Subjects to Teachers. | Relationship check in DB. |
| A3 | Student Enrollment| `backend-specialist` | Register Students and link them to Guardians and Classes. | Student profile view. |

### Phase 3: Teacher Module (P1)
| Task ID | Name | Agent | Description | Verify |
|---------|------|-------|-------------|--------|
| T1 | Attendance Sheet | `frontend-specialist` | Interactive grid to mark attendance for a specific date/class. | State update check. |
| T2 | Grade Entry | `backend-specialist` | Logic to create assessments (provas/trabalhos) and assign grades. | Avg calculation check. |
| T3 | Class Diary | `backend-specialist` | Daily log of what was taught in class. | Index/Store functionality. |

### Phase 4: Guardian Module (P2)
| Task ID | Name | Agent | Description | Verify |
|---------|------|-------|-------------|--------|
| G1 | Student Dashboard | `frontend-specialist` | View for parents to see their children's latest grades and attendance. | Profile matching check. |

---

## Phase X: Verification Checklist
- [ ] No purple/violet hex codes (Purple Ban)
- [ ] Tailwind 4 standard patterns (no legacy utilities)
- [ ] Socratic Gate answers fully respected
- [ ] **Security**: `python .agent/skills/vulnerability-scanner/scripts/security_scan.py .`
- [ ] **UX Audit**: `python .agent/skills/frontend-design/scripts/ux_audit.py .`
- [ ] **Build Check**: `npm run build`
