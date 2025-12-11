/* ===========================
   Personal Budget Dashboard JavaScript
   =========================== */

/**
 * Form Validation Functions
 */

// Validate Email
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

// Validate Amount
function validateAmount(amount) {
  const num = parseFloat(amount);
  return !isNaN(num) && num > 0;
}

// Validate Date
function validateDate(date) {
  const d = new Date(date);
  return d instanceof Date && !isNaN(d);
}

// Validate Password Strength
function validatePassword(password) {
  return password.length >= 6;
}

// Validate Form
function validateForm(formId) {
  // Skip validation if form ID is empty/undefined
  if (!formId) return true;

  const form = document.getElementById(formId);
  if (!form) return true; // Allow submission if form not found

  let isValid = true;
  const inputs = form.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );

  inputs.forEach((input) => {
    if (!input.value.trim()) {
      input.classList.add("is-invalid");
      isValid = false;
    } else {
      input.classList.remove("is-invalid");

      // Type-specific validation
      if (input.type === "email" && !validateEmail(input.value)) {
        input.classList.add("is-invalid");
        isValid = false;
      } else if (
        input.name &&
        input.name.includes("amount") &&
        !validateAmount(input.value)
      ) {
        input.classList.add("is-invalid");
        isValid = false;
      } else if (input.type === "date" && !validateDate(input.value)) {
        input.classList.add("is-invalid");
        isValid = false;
      } else {
        input.classList.remove("is-invalid");
      }
    }
  });

  return isValid;
}

// Real-time field validation
document.addEventListener("DOMContentLoaded", function () {
  // Email validation
  const emailInputs = document.querySelectorAll('input[type="email"]');
  emailInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      if (this.value && !validateEmail(this.value)) {
        this.classList.add("is-invalid");
        showMessage(this, "Invalid email format", "error");
      } else {
        this.classList.remove("is-invalid");
      }
    });
  });

  // Amount validation
  const amountInputs = document.querySelectorAll('input[type="number"]');
  amountInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      if (this.value && !validateAmount(this.value)) {
        this.classList.add("is-invalid");
        showMessage(this, "Amount must be greater than 0", "error");
      } else {
        this.classList.remove("is-invalid");
      }
    });
  });

  // Date validation
  const dateInputs = document.querySelectorAll('input[type="date"]');
  dateInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      if (this.value && !validateDate(this.value)) {
        this.classList.add("is-invalid");
        showMessage(this, "Invalid date format", "error");
      } else {
        this.classList.remove("is-invalid");
      }
    });
  });

  // Password match validation
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm_password");

  if (confirmPasswordInput) {
    confirmPasswordInput.addEventListener("blur", function () {
      if (passwordInput && this.value !== passwordInput.value) {
        this.classList.add("is-invalid");
        showMessage(this, "Passwords do not match", "error");
      } else {
        this.classList.remove("is-invalid");
      }
    });
  }

  // Form submission
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!validateForm(this.id)) {
        e.preventDefault();
        showNotification("Please fill out all fields correctly", "error");
      }
    });
  });
});

/**
 * Utility Functions
 */

// Show temporary message on input
function showMessage(element, message, type) {
  let feedback = element.nextElementSibling;
  if (!feedback || !feedback.classList.contains("invalid-feedback")) {
    feedback = document.createElement("div");
    feedback.classList.add("invalid-feedback");
    element.parentNode.insertBefore(feedback, element.nextSibling);
  }
  feedback.textContent = message;
}

// Show notification
function showNotification(message, type = "info") {
  const alertClass = {
    success: "alert-success",
    error: "alert-danger",
    warning: "alert-warning",
    info: "alert-info",
  };

  const alert = document.createElement("div");
  alert.className = `alert ${
    alertClass[type] || alertClass["info"]
  } alert-dismissible fade show`;
  alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

  const container = document.querySelector(".content-area");
  if (container) {
    container.insertBefore(alert, container.firstChild);
  }

  // Auto dismiss after 5 seconds
  setTimeout(() => {
    alert.remove();
  }, 5000);
}

// Format currency
function formatCurrency(amount) {
  return (
    "$" +
    parseFloat(amount)
      .toFixed(2)
      .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
  );
}

// Format date
function formatDate(date) {
  const options = { year: "numeric", month: "short", day: "numeric" };
  return new Date(date).toLocaleDateString("en-US", options);
}

// Confirm delete action
function confirmDelete(message = "Are you sure you want to delete this item?") {
  return confirm(message);
}

/**
 * Chart Functions
 */

// Get chart colors
function getChartColors(count = 10) {
  const colors = [
    "#FF6B6B",
    "#4ECDC4",
    "#45B7D1",
    "#FFA07A",
    "#98D8C8",
    "#F7DC6F",
    "#BB8FCE",
    "#85C1E2",
    "#F8B88B",
    "#A9DFBF",
  ];
  return colors.slice(0, count);
}

/**
 * Mobile Menu Toggle
 */

function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  if (sidebar) {
    sidebar.classList.toggle("active");
  }
}

// Close sidebar when a link is clicked on mobile
document.addEventListener("DOMContentLoaded", function () {
  const sidebarLinks = document.querySelectorAll(".sidebar-nav .nav-link");
  sidebarLinks.forEach((link) => {
    link.addEventListener("click", function () {
      if (window.innerWidth < 768) {
        toggleSidebar();
      }
    });
  });

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", function (event) {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.querySelector(".sidebar-toggle-btn");

    if (
      sidebar &&
      toggleBtn &&
      !sidebar.contains(event.target) &&
      !toggleBtn.contains(event.target) &&
      sidebar.classList.contains("active")
    ) {
      toggleSidebar();
    }
  });
});

/**
 * Search and Filter Functions
 */

// Search in table
function searchTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);

  if (!input || !table) return;

  input.addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = table
      .getElementsByTagName("tbody")[0]
      .getElementsByTagName("tr");

    rows.forEach((row) => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });
}

// Filter table by column
function filterTableByColumn(selectId, columnIndex, tableId) {
  const select = document.getElementById(selectId);
  const table = document.getElementById(tableId);

  if (!select || !table) return;

  select.addEventListener("change", function () {
    const filter = this.value.toLowerCase();
    const rows = table
      .getElementsByTagName("tbody")[0]
      .getElementsByTagName("tr");

    rows.forEach((row) => {
      const cells = row.getElementsByTagName("td");
      if (cells[columnIndex]) {
        const text = cells[columnIndex].textContent.toLowerCase();
        row.style.display =
          text.includes(filter) || filter === "" ? "" : "none";
      }
    });
  });
}

/**
 * Export Functions
 */

// Export table to CSV
function exportTableToCSV(tableId, filename = "export.csv") {
  const table = document.getElementById(tableId);
  if (!table) return;

  let csv = [];
  const rows = table.querySelectorAll("tr");

  rows.forEach((row) => {
    const cols = row.querySelectorAll("td, th");
    const csvRow = [];
    cols.forEach((col) => {
      csvRow.push('"' + col.textContent.trim() + '"');
    });
    csv.push(csvRow.join(","));
  });

  downloadCSV(csv.join("\n"), filename);
}

// Download CSV
function downloadCSV(csv, filename) {
  const link = document.createElement("a");
  link.href = "data:text/csv;charset=utf-8," + encodeURIComponent(csv);
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

/**
 * Date Range Picker
 */

// Set date range
function setDateRange(rangeType) {
  const today = new Date();
  let startDate = new Date();

  switch (rangeType) {
    case "today":
      startDate = new Date();
      break;
    case "week":
      startDate = new Date(today.setDate(today.getDate() - 7));
      break;
    case "month":
      startDate = new Date(today.getFullYear(), today.getMonth(), 1);
      break;
    case "year":
      startDate = new Date(today.getFullYear(), 0, 1);
      break;
  }

  return {
    start: formatDateForInput(startDate),
    end: formatDateForInput(new Date()),
  };
}

// Format date for input
function formatDateForInput(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
}

/**
 * Number Formatting
 */

// Format number with thousands separator
function formatNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Format as percentage
function formatPercentage(value, total) {
  if (total === 0) return "0%";
  return ((value / total) * 100).toFixed(1) + "%";
}

/**
 * Loading States
 */

// Show loading state
function showLoading(buttonId) {
  const button = document.getElementById(buttonId);
  if (button) {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
    button.dataset.originalText = originalText;
  }
}

// Hide loading state
function hideLoading(buttonId) {
  const button = document.getElementById(buttonId);
  if (button) {
    button.disabled = false;
    button.innerHTML = button.dataset.originalText || "Submit";
  }
}

/**
 * Local Storage Functions
 */

// Save to local storage
function saveToStorage(key, value) {
  try {
    localStorage.setItem(key, JSON.stringify(value));
  } catch (e) {
    console.error("Failed to save to storage:", e);
  }
}

// Get from local storage
function getFromStorage(key, defaultValue = null) {
  try {
    const item = localStorage.getItem(key);
    return item ? JSON.parse(item) : defaultValue;
  } catch (e) {
    console.error("Failed to get from storage:", e);
    return defaultValue;
  }
}

// Remove from local storage
function removeFromStorage(key) {
  try {
    localStorage.removeItem(key);
  } catch (e) {
    console.error("Failed to remove from storage:", e);
  }
}

/**
 * Initialize on Page Load
 */

document.addEventListener("DOMContentLoaded", function () {
  // Auto-dismiss alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });

  // Enable Bootstrap tooltips
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
  );

  // Enable Bootstrap popovers
  const popoverTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="popover"]')
  );
  popoverTriggerList.map(
    (popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl)
  );
});
