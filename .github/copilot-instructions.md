### **Environment**

- All code runs inside a **Docker container**.
- **Do not run PHP tests locally**; assume containerized execution only.

### **Moodle Version**

- All code must be fully compatible with **Moodle 5.1**.

### **Reference Files**

- The `stub/` folder contains **core Moodle 5.1 course format files**.
- These files are **read-only** and serve as a **reference only**.
- **Never modify or update any file in `stub/`.**

### **Coding Rules**

- Use the `stub/` folder for **API and structure reference** when implementing new features or fixing issues.
- Follow **Moodle 5.1 coding standards** and best practices. Available in the `stub/` folder.
- Ensure all plugin code is properly namespaced and adheres to Moodle’s plugin architecture.

- Ensure all CSS and HTML plugin  code is compatible with bootstrap 5 and update bootstrap 4 classes to bootstrap version 5 compatible classes.

### **Testing**

- All tests must be executed inside the **Docker container** environment.
- Do not assume local execution for any scripts or tests.

### **GitHub Copilot Usage**

- Before making any code changes, **Copilot must explain exactly what it plans to do** based on your prompt.
- You must **explicitly confirm** the explanation before Copilot proceeds with generating or modifying code.
- If the explanation does not match your intent, **revise your prompt** and request clarification before approving changes. 

### Coding Style
- Always follow Moodle coding style as per the Moodle 5.1 standards.
- File coding_styles_moodle.html contains the coding style guidelines.