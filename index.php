<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>E-Book Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {
      --primary: #8e44ad;
      --primary-dark: #7d3c98;
      --success: #27ae60;
      --success-dark: #219a52;
      --text: #2c3e50;
      --text-light: #7f8c8d;
      --background: #f5f5f5;
      --white: #ffffff;
      --border: #FFD700; /* changed to yellow */
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      color: var(--text);
      position: relative;
      background: url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=228&auto=format&fit=crop') center/cover no-repeat fixed;
    }

    /* Overlay for better readability */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: 1;
    }

    .container {
      background: linear-gradient(rgba(255, 255, 255, 0.85), rgba(255, 255, 255, 0.85)),
                  url('https://images.unsplash.com/photo-1457369804613-52c61a468e7d?q=80&w=1000&auto=format&fit=crop') center/cover no-repeat;
      border-radius: 4px;
      max-width: 800px;
      width: 100%;
      padding: 40px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      display: flex;
      flex-direction: column;
      gap: 1.8rem;
      position: relative;
      z-index: 2;
      backdrop-filter: blur(10px);
      border: 1px solid var(--border);
    }

    .container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(5px);
      border-radius: 4px;
      z-index: -1;
    }

    h1 {
      font-size: 2.5rem;
      color: var(--primary);
      font-weight: 500;
      letter-spacing: -0.5px;
      margin-bottom: 0.2rem;
      text-align: center;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    h3 {
      font-size: 1.4rem;
      color: var(--text);
      font-weight: 500;
      margin-bottom: 1rem;
      text-align: center;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }

    p.lead {
      font-size: 1.1rem;
      color: var(--text);
      margin-bottom: 1.8rem;
      line-height: 1.6;
      text-align: center;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }

    .buttons {
      display: flex;
      gap: 1.4rem;
      justify-content: center;
      flex-wrap: wrap;
      margin: 20px 0;
      position: relative;
      z-index: 3;
    }

    .buttons a {
      flex: 1;
      max-width: 200px;
      padding: 12px 24px;
      font-size: 14px;
      font-weight: 500;
      border-radius: 4px;
      text-decoration: none;
      text-align: center;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-login {
      background-color: var(--primary);
      color: var(--white);
    }
    .btn-login:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(142, 68, 173, 0.2);
    }

    .btn-register {
      background-color: var(--success);
      color: var(--white);
    }
    .btn-register:hover {
      background-color: var(--success-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(39, 174, 96, 0.2);
    }

    .btn-admin {
      background-color: var(--text);
      color: var(--white);
    }
    .btn-admin:hover {
      background-color: #1a252f;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2);
    }

    .terms-checkbox {
      color: var(--text);
      font-size: 14px;
      display: flex;
      justify-content: center;
      gap: 0.5rem;
      align-items: center;
      margin: 20px 0;
    }
    .terms-checkbox input {
      width: 16px;
      height: 16px;
      cursor: pointer;
    }
    .terms-checkbox a {
      color: var(--primary);
      text-decoration: none;
      cursor: pointer;
      transition: color 0.2s;
    }
    .terms-checkbox a:hover {
      color: var(--primary-dark);
    }

    .about {
      background-color: rgba(255, 255, 255, 0.9);
      border: 1px solid var(--border);
      padding: 24px;
      border-radius: 4px;
      color: var(--text);
      text-align: left;
      backdrop-filter: blur(5px);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    .about h4 {
      margin-bottom: 12px;
      font-size: 1.2rem;
      font-weight: 500;
      color: var(--primary);
    }
    .about p {
      color: var(--text);
      line-height: 1.6;
      font-size: 14px;
    }

    footer {
      margin-top: 20px;
      font-size: 14px;
      color: var(--text-light);
      text-align: center;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(5px);
    }

    .modal-content {
      background-color: var(--white);
      margin: 5% auto;
      padding: 30px;
      border-radius: 4px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      border: 1px solid var(--border); /* add yellow border here as well */
    }

    .modal-content h2 {
      color: var(--primary);
      font-size: 1.5rem;
      margin-bottom: 20px;
      font-weight: 500;
    }

    .modal-content ul {
      margin: 20px 0 20px 24px;
      list-style: disc;
    }

    .modal-content ul li {
      margin-bottom: 10px;
      color: var(--text);
      font-size: 14px;
      line-height: 1.6;
    }

    .modal-content p {
      color: var(--text);
      font-size: 14px;
      line-height: 1.6;
    }

    .close {
      float: right;
      font-size: 20px;
      font-weight: 500;
      color: var(--text-light);
      cursor: pointer;
      transition: color 0.2s;
    }

    .close:hover {
      color: var(--text);
    }

    @media (max-width: 600px) {
      .container {
        padding: 30px 20px;
      }
      h1 {
        font-size: 2rem;
      }
      h3 {
        font-size: 1.2rem;
      }
      .buttons {
        flex-direction: column;
      }
      .buttons a {
        max-width: 100%;
      }
      .about {
        padding: 20px;
      }
      .modal-content {
        padding: 20px;
        margin: 10% auto;
      }
    }

  </style>
</head>
<body>

  <div class="container">
    <h1>📚E-Bow-Ook Portal</h1>
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

    document.querySelector('.close').addEventListener('keydown', function(e) {
      if(e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        closeModal();
      }
    });
  </script>

</body>
</html>

