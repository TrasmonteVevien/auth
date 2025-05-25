<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>NBSC Book Management System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {
      --yellow: #ffd633;
      --black: #000000;
      --green: #4caf50;
      --blue: #2196f3;
      --white: #ffffff;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, var(--black), var(--blue), var(--green));
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      color: var(--white);
      text-align: center;
    }

    .container {
      background-color: #222; /* lighter dark gray */
      border-radius: 20px;
      max-width: 700px;
      width: 100%;
      padding: 2.5rem 3rem;
      box-shadow: 0 0 10px #ffd63388; /* softer yellow glow */
      border: 2px solid #ffd633cc; /* softer yellow border */
      display: flex;
      flex-direction: column;
      gap: 1.8rem;
    }

    h1 {
      font-size: 3rem;
      color: var(--yellow);
      letter-spacing: 0.1em;
      margin-bottom: 0.2rem;
      user-select: none;
    }

    h3 {
      font-size: 1.6rem;
      color: var(--green);
      font-weight: 600;
      margin-bottom: 1.4rem;
      user-select: none;
    }

    p.lead {
      font-size: 1.2rem;
      color: var(--white);
      margin-bottom: 1.8rem;
      line-height: 1.5;
      font-weight: 500;
    }

    /* Button group */
    .buttons {
      display: flex;
      gap: 1.4rem;
      justify-content: center;
      flex-wrap: wrap;
    }

    .buttons a {
      flex: 1 1 140px;
      padding: 0.9rem 1.5rem;
      font-size: 1.1rem;
      font-weight: 700;
      border-radius: 12px;
      text-decoration: none;
      color: var(--black);
      box-shadow:
        0 4px 10px rgba(0, 0, 0, 0.25);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      background-color: var(--yellow);
    }
    .btn-login {
      background-color: var(--blue);
      color: var(--white);
    }
    .btn-login:hover {
      background-color: #1769aa;
      box-shadow: 0 6px 15px #1769aaaa;
      transform: translateY(-3px);
    }

    .btn-register {
      background-color: var(--green);
      color: var(--white);
    }
    .btn-register:hover {
      background-color: #357a38;
      box-shadow: 0 6px 15px #357a3899;
      transform: translateY(-3px);
    }

    .btn-admin {
      background-color: var(--yellow);
      color: var(--black);
      font-weight: 800;
    }
    .btn-admin:hover {
      background-color: #e6c700;
      box-shadow: 0 6px 15px #e6c700cc;
      transform: translateY(-3px);
    }

    /* Terms section */
    .terms-checkbox {
      color: var(--white);
      font-size: 1rem;
      display: flex;
      justify-content: center;
      gap: 0.5rem;
      align-items: center;
      user-select: none;
      margin-top: 1rem;
    }
    .terms-checkbox input {
      width: 18px;
      height: 18px;
      cursor: pointer;
    }
    .terms-checkbox a {
      color: var(--yellow);
      text-decoration: underline;
      cursor: pointer;
      font-weight: 600;
      transition: color 0.3s ease;
    }
    .terms-checkbox a:hover {
      color: var(--green);
    }

    /* About Section */
    .about {
      background-color: var(--black);
      border: 2px solid var(--yellow);
      padding: 1.6rem 2rem;
      border-radius: 15px;
      color: var(--yellow);
      text-align: left;
      font-weight: 600;
      box-shadow: 0 0 10px var(--yellow);
    }
    .about h4 {
      margin-bottom: 0.6rem;
      font-size: 1.5rem;
      user-select: none;
    }
    .about p {
      font-weight: 500;
      color: var(--white);
      line-height: 1.4;
    }

    footer {
      margin-top: 2rem;
      font-size: 0.9rem;
      color: var(--yellow);
      user-select: none;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(3px);
      overflow-y: auto;
      padding: 2rem 1rem;
    }

    .modal-content {
      background-color: var(--black);
      margin: 5% auto;
      padding: 2rem;
      border-radius: 15px;
      width: 90%;
      max-width: 550px;
      box-shadow: 0 0 25px var(--yellow);
      color: var(--white);
      font-weight: 500;
      user-select: text;
    }

    .modal-content h2 {
      color: var(--yellow);
      font-size: 2rem;
      margin-bottom: 1rem;
      user-select: none;
    }

    .modal-content ul {
      margin-left: 1.3rem;
      margin-bottom: 1rem;
      list-style: disc;
    }

    .modal-content ul li {
      margin-bottom: 0.7rem;
    }

    .close {
      float: right;
      font-size: 28px;
      font-weight: bold;
      color: var(--yellow);
      cursor: pointer;
      transition: color 0.3s ease;
      user-select: none;
    }

    .close:hover {
      color: var(--green);
    }

    @media (max-width: 600px) {
      .container {
        padding: 2rem 1.5rem;
      }
      h1 {
        font-size: 2.4rem;
      }
      h3 {
        font-size: 1.3rem;
      }
      .buttons a {
        flex: 1 1 100%;
      }
      .about {
        font-size: 0.9rem;
      }
      .modal-content {
        padding: 1.4rem;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <h1>📚 NBSC Book Management</h1>
    <h3>North Bukidnon State College</h3>
    <p class="lead">Effortlessly manage and track your books with our secure platform designed for NBSC students and staff.</p>

    <div class="buttons" aria-label="Main system actions">
      <a href="login.php" class="btn-login" aria-label="Login to system">Login</a>
      <a href="register.php" class="btn-register" aria-label="Register new account">Register</a>
      <a href="admin_login.php" class="btn-admin" aria-label="Admin login panel">Admin</a>
    </div>

    <div class="terms-checkbox">
      <input type="checkbox" id="agreeCheckbox" aria-required="true" />
      <label for="agreeCheckbox">I agree to the <a href="#" onclick="openModal()" role="button" tabindex="0">terms and agreement</a>.</label>
    </div>

    <section class="about" aria-labelledby="about-title">
      <h4 id="about-title">About the System</h4>
      <p>This platform allows users to register, borrow, return, and track books. Admins monitor user activity and ensure data integrity.</p>
    </section>

    <footer>
      &copy; Loren D., V.Althia T. | 2025 Borrowing Books Management System. All rights reserved.
    </footer>
  </div>

  <!-- Modal -->
  <div id="termsModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalDesc">
    <div class="modal-content">
      <span class="close" role="button" tabindex="0" aria-label="Close terms and agreement" onclick="closeModal()">&times;</span>
      <h2 id="modalTitle">Terms and Agreement</h2>
      <div id="modalDesc">
        <p>By using the NBSC Book Management System, you agree to the following:</p>
        <ul>
          <li>You must provide accurate and truthful information during registration.</li>
          <li>Borrowed books must be returned on or before the due date.</li>
          <li>Unauthorized access or misuse of the system may result in account suspension.</li>
          <li>Your activity may be logged for security and monitoring purposes.</li>
        </ul>
        <p>Please contact your department librarian for further questions.</p>
      </div>
    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById('termsModal').style.display = 'block';
    }

    function closeModal() {
      document.getElementById('termsModal').style.display = 'none';
    }

    window.onclick = function(event) {
      const modal = document.getElementById('termsModal');
      if (event.target === modal) {
        closeModal();
      }
    };

    // Accessibility: allow close modal with keyboard
    document.querySelector('.close').addEventListener('keydown', function(e) {
      if(e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        closeModal();
      }
    });
  </script>

</body>
</html>

