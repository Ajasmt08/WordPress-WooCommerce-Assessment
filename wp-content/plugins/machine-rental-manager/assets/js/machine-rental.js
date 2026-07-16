document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("mrm-rental-form");
  if (!form) return;

  const msgDiv = document.getElementById("mrm-message");
  const submitBtn = document.getElementById("mrm_submit_btn");
  const dateFrom = document.getElementById("mrm_date_from");
  const dateTo = document.getElementById("mrm_date_to");

  function getFormattedDate(dateObj) {
    const year = dateObj.getFullYear();
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const day = String(dateObj.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  const today = getFormattedDate(new Date());
  dateFrom.setAttribute('min', today);
  dateTo.setAttribute('min', today);

  dateFrom.addEventListener('change', function () {
    if (this.value) {
      const fromDateObj = new Date(this.value);
      fromDateObj.setDate(fromDateObj.getDate() + 1);
      const nextDay = getFormattedDate(fromDateObj);
      dateTo.setAttribute('min', nextDay);
      if (dateTo.value && dateTo.value <= this.value) {
        dateTo.value = '';
      }
    }
  });

  function showError(message) {
    msgDiv.innerHTML = `<div style="background:#f8d7da; color:#721c24; padding:10px; border-radius:4px;">${message}</div>`;
    submitBtn.disabled = false;
    submitBtn.value = "Submit Request";
  }

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    msgDiv.innerHTML = "";
    submitBtn.disabled = true;
    submitBtn.value = "Validating...";

    const fullName = document.getElementById("mrm_full_name").value.trim();
    const email = document.getElementById("mrm_email").value.trim();
    const phone = document.getElementById("mrm_phone").value.trim();
    const location = document.getElementById("mrm_location").value.trim();
    const machineType = document.getElementById("mrm_machine_type").value.trim();

    // 1. Empty Field Validation (Replacing HTML 'required')
    if (!fullName || !email || !phone || !dateFrom.value || !dateTo.value || !location || !machineType) {
      return showError('All fields are required. Please fill out the entire form.');
    }

    const rentalPeriod = "From: " + dateFrom.value + " To: " + dateTo.value;

    // 2. Date Validation
    if (new Date(dateFrom.value) >= new Date(dateTo.value)) {
      return showError('The "To" date must be strictly after the "From" date.');
    }

    // 3. Phone Validation
    if (phone.length < 8 || phone.length > 13) {
      return showError('Phone number must be between 8 and 13 digits long.');
    }

    // 4. Email Validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return showError('Please enter a valid email address.');
    }

    // 5. Max Length Validation (Replacing HTML 'maxlength')
    if (fullName.length > 100 || email.length > 100 || location.length > 100 || machineType.length > 100 || rentalPeriod.length > 100) {
      return showError('Inputs cannot exceed 100 characters.');
    }

    submitBtn.value = "Submitting...";

    const payload = {
      full_name: fullName,
      email: email,
      phone: phone,
      rental_period: rentalPeriod,
      location: location,
      machine_type: machineType
    };

    fetch(mrm_api_config.rest_url + 'machine-rental/v1/submit', {
      method: 'POST',
      credentials: 'same-origin', // Sends session cookie for wp_verify_nonce
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': mrm_api_config.nonce
      },
      body: JSON.stringify(payload)
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          msgDiv.innerHTML = '<div style="background:#d4edda; color:#155724; padding:10px; border-radius:4px;"><strong>Success!</strong> Your rental request has been received.</div>';
          form.reset();
          dateTo.setAttribute('min', today);
        } else {
          showError('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        showError('A network error occurred. Please try again.');
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.value = "Submit Request";
      });
  });
});