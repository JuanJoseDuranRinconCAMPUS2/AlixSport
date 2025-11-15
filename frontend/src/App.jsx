import { useState, useEffect } from "react";
import LoginPage from "./pages/login";
import RegisterPage from "./pages/RegisterPage";
import HomePage from "./pages/HomePage";
import ForgotPassword from "./pages/ForgotPasword";
import VerificationCode from "./pages/VerificacionCode";

export default function App() {
  const [user, setUser] = useState(null);
  const [page, setPage] = useState("home");
  const [recoveryEmail, setRecoveryEmail] = useState("");

  useEffect(() => {
    const storedUser = localStorage.getItem("user");
    if (storedUser) setUser(JSON.parse(storedUser));
  }, []);

  const handleLogin = (userData) => {
    setUser(userData);
    localStorage.setItem("user", JSON.stringify(userData));
    setPage("home");
  };

  const handleLogout = () => {
    localStorage.removeItem("user");
    setUser(null);
  };

  const handleForgotPassword = () => {
    setPage("forgot-password");
  };

  const handleCodeSent = (email) => {
    setRecoveryEmail(email);
    setPage("verification");
  };

  const handleBackToEmail = () => {
    setPage("forgot-password");
  };

  const handleVerificationSuccess = () => {
    setPage("login");
    setRecoveryEmail("");
  };

  return (
    <>
      {page === "home" && (
        <HomePage
          user={user}
          onLoginClick={() => setPage("login")}
          onRegisterClick={() => setPage("register")}
          onLogout={handleLogout}
        />
      )}

      {page === "login" && (
        <LoginPage 
          onLogin={handleLogin} 
          onRegisterClick={() => setPage("register")} 
          onForgotPasswordClick={handleForgotPassword}
        />
      )}

      {page === "register" && <RegisterPage onGoLogin={() => setPage("login")} />}

      {page === "forgot-password" && (
        <ForgotPassword 
          onBackToLogin={() => setPage("login")}
          onCodeSent={handleCodeSent}
        />
      )}

      {page === "verification" && (
        <VerificationCode 
          email={recoveryEmail}
          onBackToEmail={handleBackToEmail}
          onVerificationSuccess={handleVerificationSuccess}
        />
      )}
    </>
  );
}