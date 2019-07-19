'use strict'

document.addEventListener('DOMContentLoaded', e => {
  let date = new Date()
  const year = date.getFullYear()
  let yearContent = document.querySelector('#year')
  yearContent.innerHTML = year

  // Add intl-tel-input
  window.intlTelInputGlobals.loadUtils('scripts/utils.js')
  var input = document.querySelector('#phone')
  window.intlTelInput(input, {
    initialCountry: 'ng',
    separateDialCode: true,
    hiddenInput: 'full_phone',
    utilsScript: 'scripts/utils.js'
  })

  // Change the typed value of the first letter to uppercase for input fields and lowercase for email fields
  document.querySelector('#name').onchange = e => {
    let val = document.querySelector('#name').value
    RegExp = /\b[a-z]/g

    val = val.charAt(0).toUpperCase() + val.substr(1)
  }

  document.querySelector('#email').onchange = e => {
    let val = document.querySelector('#email').value
    RegExp = /\b[a-z]/g

    val = val.toLowerCase()
  }

  //   Submit the form
  const form = document.querySelector('form')
  // On Form Submit
  form.addEventListener('submit', e => {
    let forms = document.getElementsByClassName('needs-validation')
    // Check to see if form has validation errors
    let validation = Array.prototype.filter.call(forms, form => {
      if (form.checkValidity() === false) {
        e.preventDefault()
        e.stopPropagation()
      }
      form.classList.add('was-validated')
    })

    // If form doesn't have validation errors
    if (form.checkValidity() === true) {
      e.preventDefault()

      // change the button color and add the loading class
      document.querySelector('button').classList.remove('btn-danger')
      document.querySelector('button').classList.add('btn-primary')
      document.querySelector('button').innerHTML =
        'Loading <span class="spinner"></span><i class="fa fa-spinner fa-spin"></i></span>'

      const formdata = new FormData(form)

      // initiate a fetch call
      fetch('scripts/submit.php', {
        method: 'post',
        body: formdata
      })
        .then(response => {
          return response.json()
        })
        .then(data => {
        //   console.log(data)
            if (data === 'user_exists') {
              swal(
                'Already Registered',
                'You have already registered for the webinar.',
                'warning'
              )
              setTimeout(() => {
                window.location = 'https://businessitygroup.com'
              }, 3000)
            } else if (data === 'success') {
              swal(
                'Registration Successful!',
                'Your registration was successful.',
                'success'
              )
              setTimeout(() => {
                window.location = 'https://businessitygroup.com'
              }, 3000)
            }
        })
        .catch(error => {
          console.log('The Request has Failed', error)
        })
    }
  })
})
