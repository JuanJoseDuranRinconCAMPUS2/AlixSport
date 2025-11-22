import { useState, useEffect } from "react";
import LoginPage from "./pages/login";
import RegisterPage from "./pages/RegisterPage";
import HomePage from "./pages/HomePage";
import ForgotPassword from "./pages/ForgotPasword";
import VerificationCode from "./pages/VerificacionCode";
import ProductDetail from "./pages/ProductDetail";
import AdminPanel from "./pages/AdminPanel";
import axios from "axios";

const API = import.meta.env.VITE_API;

export default function App() {
  const [user, setUser] = useState(null);
  const [page, setPage] = useState("home");
  const [recoveryEmail, setRecoveryEmail] = useState("");
  const [cart, setCart] = useState(() => {
    const stored = localStorage.getItem("cart");
    return stored ? JSON.parse(stored) : [];
  });
  const [selectedProduct, setSelectedProduct] = useState(null);

  
  useEffect(() => {
    const storedUser = localStorage.getItem("user");
    if (storedUser) {
      const parsedUser = JSON.parse(storedUser);
      setUser(parsedUser);
      getCartInfoUser(parsedUser.id);
    }
  }, []);

  const handleLogin = (userData) => {
    setUser(userData);
    localStorage.setItem("user", JSON.stringify(userData));
    getCartInfoUser(userData.id);
    setPage("home");

  };

  const handleLogout = () => {
    localStorage.removeItem("user");
    localStorage.removeItem("cart");
    setUser(null);
    setCart([]);
  };

  const handleViewProduct = (product) => {
    setSelectedProduct(product);
    setPage("product-detail");
  };

  const getCartInfoUser = async (userId) => {
    const { data } = await axios.post(`${API}/DetallesByUser`, 
      {
        "idUsuario": `${userId}`
      }
    ); 
    
    if (data) {
      setCart(data);
      localStorage.setItem("cart", JSON.stringify(data));
    }
  };

  const addToCart = async (product, quantity) => {
    console.log(user);
    console.log(product);
    console.log(quantity);
    const { data } = await axios.post(`${API}/cambiarCantidadCarrito`, 
      {
        "idUsuario": `${user.id}`,
        "idProducto": `${product.id_Producto}`,
        "cantidad": `${quantity}`
        
      }
    ); 
    await getCartInfoUser(user.id);
    console.log(data);
  };

  const removeFromCart = async (productId) => {
    const { data } = await axios.post(`${API}/cambiarCantidadCarrito`, 
      {
        "idUsuario": `${user.id}`,
        "idProducto": `${productId}`,
        "cantidad": `0`
        
      }
    ); 
    await getCartInfoUser(user.id);
    console.log(data);
    
  };

  const updateQuantity = async (productId, newQuantity) => {
    const { data } = await axios.post(`${API}/cambiarCantidadCarrito`, 
      {
        "idUsuario": `${user.id}`,
        "idProducto": `${productId}`,
        "cantidad": `${newQuantity}`
        
      }
    ); 
    await getCartInfoUser(user.id);
    console.log(data);
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
          cart={cart}
          onAddToCart={addToCart}
          onRemoveFromCart={removeFromCart}
          onUpdateQuantity={updateQuantity}
          onViewProduct={handleViewProduct}
          onAdminClick={() => setPage("admin")}
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

      {page === "product-detail" && selectedProduct && (
        <ProductDetail
          product={selectedProduct}
          onBack={() => setPage("home")}
          onAddToCart={addToCart}
          user={user}
        />
      )}

      {page === "admin" && (
        <AdminPanel
          onBack={() => setPage("home")}
        />
      )}
    </>
  );
}