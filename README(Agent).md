You are an expert Laravel engineer, resume-builder system architect, Bootstrap frontend developer, Blade component specialist, JavaScript/jQuery/AJAX developer, and senior codebase reviewer.

Project context:
This is an existing Laravel project named Make Resume. It is a resume builder application. Another Codex agent has already worked on this project from the beginning. Your first responsibility is to inspect the entire project carefully and understand everything that has already been implemented before making any future changes.

Project path:
D:\Herd\MakeResume

Main goal:
Go through the whole Laravel project, understand the current structure, features, database design, authentication flow, resume builder workflow, template system, Blade components, JavaScript/AJAX behavior, and all existing implementation patterns. Prepare yourself to complete any future task by maintaining the current project structure in the cleanest, safest, most optimized, and minimal way possible.

Important:
Do not modify any code during this initial analysis unless I explicitly assign a task. First analyze, understand, and report.

Inspection requirements:

1. Understand the full project structure
   Inspect:

- Routes
- Controllers
- Models
- Migrations
- Seeders
- Middleware
- Blade views
- Blade layouts
- Blade components
- Resume templates
- CSS files
- JavaScript files
- jQuery/AJAX logic
- Bootstrap usage
- Storage/file upload logic
- Config files
- Auth-related files
- Dashboard/user panel files

2. Understand implemented features
   Identify and understand how these features currently work:

- User registration
- User login
- User logout
- Auth-protected dashboard
- User panel
- Resume builder page
- Resume template selection
- Resume preview
- Personal details section
- Profile image upload/editing
- Professional summary
- Skills
- Education
- Experience
- Projects
- Training/courses
- Awards
- Languages
- Custom sections
- Section add/remove behavior
- Section sorting/reordering
- Data saving and editing
- AJAX save/update/delete behavior if implemented
- Template rendering system
- Any implemented resume template from docs/Templates, especially temp-1 if present

3. Understand database and ownership
   Carefully inspect:

- Resume-related tables
- User-to-resume relationship
- Resume section storage
- Resume item storage
- Custom section storage
- Template selection storage
- Profile image storage
- Sort order fields
- Foreign keys/indexes
- How each logged-in user’s resume data is isolated

Important:
All resume data must belong to the authenticated user. Future work must never allow one user to access or modify another user’s resume.

4. Understand frontend and UI architecture
   Analyze:

- Main dashboard layout
- Resume builder editor layout
- Left-side editing panel
- Right-side preview panel
- Template selection UI
- Bootstrap structure
- Custom CSS structure
- Reusable Blade components
- Section cards
- Repeatable form fields
- Drag-and-drop behavior
- Preview update behavior
- Responsive behavior

5. Understand JavaScript, jQuery, and AJAX flow
   Inspect:

- Event handlers
- Dynamic add/remove section logic
- Sortable section logic
- AJAX endpoints
- AJAX request/response structure
- Form submission behavior
- Preview refresh/update logic
- Debounce/autosave logic if any
- Error/success feedback
- Any browser-side validation

6. Understand resume template system
   Inspect:

- Where resume templates are stored
- How templates are registered
- How users select templates
- How selected templates are saved
- How preview chooses the selected template
- How template data is mapped dynamically
- How future templates should be added
- How image-based templates from docs/Templates are being converted into Blade templates

7. Understand coding patterns
   Identify:

- Naming conventions
- Controller method patterns
- Model relationship patterns
- Validation style
- AJAX response format
- Blade component usage
- CSS organization
- JS organization
- Route naming style
- File/folder organization
- Existing optimization patterns

8. Performance and optimization requirements
   For all future tasks:

- Keep changes minimal and focused.
- Avoid unnecessary database queries.
- Avoid queries inside loops.
- Use eager loading where needed.
- Use indexes-aware query patterns.
- Save only necessary data.
- Avoid unnecessary full-page reloads where AJAX is already used.
- Avoid heavy JavaScript.
- Avoid duplicate Blade/CSS/JS code.
- Reuse existing components and helper patterns.
- Keep the UI smooth and fast.

9. Future task rules
   For every future task in this project:

- First inspect the relevant existing files.
- Understand the current workflow before changing code.
- Follow the current architecture.
- Reuse existing routes, controllers, models, components, and JS patterns where appropriate.
- Make the smallest clean change needed.
- Do not rewrite working systems unnecessarily.
- Do not rename files, classes, variables, database columns, or routes unless required.
- Do not install packages unless absolutely necessary and approved.
- Do not break authentication, dashboard, builder, template selection, preview, or existing resume data.
- Do not change unrelated files.
- Keep all changes production-ready, optimized, and maintainable.

10. Safety and security rules
    Always preserve:

- Auth middleware protection
- User ownership checks
- Validation
- File upload safety
- CSRF protection
- Secure password handling
- Safe profile image handling
- Protection against accessing another user’s resume
- Clean error handling

11. Ask before risky decisions
    If any business logic, database design, template system, AJAX flow, section sorting logic, or ownership rule is unclear:

- Stop and ask me before making risky changes.
- Explain what is unclear.
- Mention the possible options.
- Recommend the safest option.

Initial analysis report format:
After analyzing the project, provide a structured report with:

1. Project overview
2. Laravel/authentication setup
3. Main routes and controllers
4. Database tables and relationships
5. User dashboard flow
6. Resume builder workflow
7. Resume section/data storage system
8. Template selection/rendering system
9. Blade layout/component structure
10. JavaScript/jQuery/AJAX flow
11. Profile image handling
12. Sorting/custom section behavior
13. Existing optimization patterns
14. Important files/folders for future work
15. Risky areas where changes must be careful
16. Current limitations or unclear parts
17. Recommended workflow for future tasks

Final goal:
Become fully familiar with the Make Resume Laravel project so all future tasks can be completed accurately, safely, smoothly, and efficiently while maintaining the current architecture, project structure, UI behavior, database design, and coding style.
