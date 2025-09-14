$('#loginForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: '/api/login',
        method: 'POST',
        data: {
            email: $('#email').val(),
            password: $('#password').val()
        },
        success: function (response) {
            $('#message').text('Login successful!');
            // localStorage.setItem('employeeLogin', response);
            // You can also redirect or store token/session
            window.location.href = 'dashboard.html';
        },
        error: function (xhr) {
            $('#message').text('Login failed: ' + xhr.responseJSON.message);
        }
    });
});