<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100;0,9..40,200;0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;0,9..40,900;0,9..40,1000;1,9..40,100;1,9..40,200;1,9..40,300;1,9..40,400;1,9..40,500;1,9..40,600;1,9..40,700;1,9..40,800;1,9..40,900;1,9..40,1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <title>Edusogno</title>
</head>

<body>
    <header>
        <img src="./assets/img/logo.svg" alt="logo">
    </header>

    <main>
        <h1 class="text-center titolo">Crea il tuo account</h1>
        <div class="container">
            <form id="register-form" method="POST" action="">
                <h5><label for="name">Inserisci il nome</label></h5>
                <div><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="user-name" placeholder="Mario">
                    <span id="name-error" class="invalid-feedback" role="alert"><strong></strong></span>
                </div>

                <h5><label for="surname">Inserisci il cognome</label></h5>
                <div><input type="text" name="surname" class="form-control @error('surname') is-invalid @enderror" id="user-surname" placeholder="Rossi">
                    <span id="surname-error" class="invalid-feedback" role="alert"><strong></strong></span>
                </div>

                <h5><label for="email">Inserisci l'email</label></h5>
                <div><input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="user-email" placeholder="name@example.com">
                    <span id="email-error" class="invalid-feedback" role="alert"><strong></strong></span>
                </div>

                <h5><label for="password">Inserisci la password</label></h5>
                <div class="password-container"><input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="user-password" placeholder="Scrivila qui">
                    <i class="fa-solid fa-eye" id="eye"></i>
                    <span id="password-error" class="invalid-feedback" role="alert"><strong></strong></span>
                </div>

                <button type="button" id="registrati">REGISTRATI</button>
            </form>

            <h4 class="text-center">Hai già un account? <a href="index.php"><span>Accedi</span></a></h4>
        </div>
    </main>
</body>

<script>
    const passwordInput = document.getElementById("user-password")
    const eye = document.getElementById("eye")

    eye.addEventListener("click", function() {
        this.classList.toggle("fa-eye-slash")
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password"
        passwordInput.setAttribute("type", type)
    })

    document.addEventListener("DOMContentLoaded", function() {
        const registerForm = document.getElementById("register-form");
        const submitButton = document.getElementById("registrati");
        const nameField = document.getElementById("user-name");
        const surnameField = document.getElementById("user-surname");
        const emailField = document.getElementById("user-email");
        const passwordField = document.getElementById("user-password");


        submitButton.addEventListener("click", function() {
            if (validateForm()) {
                registerForm.submit();
            }
        });

        function validateForm() {
            let isValid = true;

            const nameValue = nameField.value.trim();
            if (nameValue === "") {
                isValid = false;
                document.getElementById("name-error").innerHTML = "Il nome è obbligatorio.";
                nameField.classList.add("is-invalid");
            } else {
                document.getElementById("name-error").innerHTML = "";
                nameField.classList.remove("is-invalid");
            }

            const surnameValue = surnameField.value.trim();
            if (surnameValue === "") {
                isValid = false;
                document.getElementById("surname-error").innerHTML = "Il cognome è obbligatorio.";
                surnameField.classList.add("is-invalid");
            } else {
                document.getElementById("surname-error").innerHTML = "";
                surnameField.classList.remove("is-invalid");
            }

            const emailValue = emailField.value.trim();
            if (emailValue === "") {
                isValid = false;
                document.getElementById("email-error").innerHTML = "L'email è obbligatoria.";
                emailField.classList.add("is-invalid");
            } else if (!isValidEmail(emailValue)) {
                isValid = false;
                document.getElementById("email-error").innerHTML = "Inserisci un indirizzo email valido.";
                emailField.classList.add("is-invalid");
            } else {
                document.getElementById("email-error").innerHTML = "";
                emailField.classList.remove("is-invalid");
            }

            const passwordValue = passwordField.value.trim();
            if (passwordValue === "") {
                isValid = false;
                document.getElementById("password-error").innerHTML =
                    "La password è obbligatoria.";
                passwordField.classList.add("is-invalid");
            } else {
                document.getElementById("password-error").innerHTML = "";
                passwordField.classList.remove("is-invalid");
            }

            return isValid;
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
</script>

<style>
    body {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        height: 100%;
        background-image: url('./assets/img/background.png');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: bottom;
        font-family: 'DM Sans', sans-serif;
    }

    header {
        background-color: white;
        padding: 1.2rem 2.5rem;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
        box-shadow: 2px 10px 15px #8cb2f7;
    }

    main {
        margin: 3rem auto;
        width: 50%;
    }

    .text-center {
        text-align: center;
    }

    .titolo {
        color: #134077;
    }

    .container {
        background-color: white;
        border-radius: 20px;
        border: 2px solid #2b5486;
        margin-bottom: 20rem;
    }

    form {
        padding: 2rem 2rem 0 2rem;
    }

    input {
        border: none;
        width: 100%;
        height: 1.5rem;
        border-bottom: 1px solid black;
        margin-bottom: 1.5rem;
    }

    ::placeholder {
        color: gray;
        opacity: 0.3;
        font-size: 1.2rem;
    }

    .invalid-feedback {
        color: red;
    }

    .password-container {
        width: 100%;
        position: relative;
    }

    .password-container input[type="password"],
    .password-container input[type="text"] {
        width: 100%;
        padding: 12px 36px 12px 0px;
        box-sizing: border-box;
    }

    .fa-eye {
        position: absolute;
        top: 5%;
        right: 2%;
        cursor: pointer;
        color: lightgray;
        scale: 1.3;
        color: blue;
    }

    #registrati {
        cursor: pointer;
        width: 100%;
        margin-top: 2rem;
        background-color: #0057ff;
        color: white;
        border-radius: 20px;
        padding: 1.2rem 0;
        height: auto;
        font-size: 1.5rem;
    }

    h4 {
        margin-bottom: 3rem;
        font-weight: normal;
        text-decoration: underline;
    }

    span {
        font-weight: bold;
        cursor: pointer;
        color: black;
    }
</style>

</html>