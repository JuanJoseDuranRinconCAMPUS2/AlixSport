import { ShoppingCart, User } from "lucide-react";
import logo from "../assets/axil par.png";

export default function Navbar({ user, onLoginClick, onRegisterClick, onLogout }) {
  
  return (
    <nav className="fixed top-0 left-0 w-full bg-gradient-to-b from-black/30 to-transparent backdrop-blur-sm text-white py-4 px-8 flex justify-between items-center z-50">
      
      <div className="flex items-center space-x-3">
        <img src={logo} alt="Logo" className="w-20 h-20" />
        <h1 className="text-xl font-bold tracking-wide">ALIX SUPLEMENTOS</h1>
      </div>

      <div className="flex items-center space-x-6 text-sm font-semibold">
        <a href="#" className="text-black hover:text-gray-100 bg-lime-600 px-3 py-2 rounded">Catálogo</a>

        {!user && (
          <button 
            onClick={onRegisterClick}
            className="text-black hover:text-gray-100 bg-lime-500 px-3 py-2 rounded"
          >
            Registro
          </button>
        )}

        <div className="flex items-center space-x-4">
          <button className="hover:text-lime-400 transition">
            <ShoppingCart size={28} />
          </button>

          {user ? (
            <div className="relative group">
              <button className="hover:text-lime-400 transition">
                <User size={28} />
              </button>
              <div className="absolute right-0 mt-2 w-48 bg-neutral-800 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                <div className="p-4">
                  <p className="text-sm text-gray-300">Hola, {user.nombre}</p>
                  <button 
                    onClick={onLogout}
                    className="w-full mt-2 bg-red-600 hover:bg-red-500 text-white py-1 px-3 rounded text-sm"
                  >
                    Cerrar Sesión
                  </button>
                </div>
              </div>
            </div>
          ) : (
            <button 
              onClick={onLoginClick}
              className="hover:text-lime-400 transition"
            >
              <User size={28} />
            </button>
          )}
        </div>
      </div>
    </nav>
  );
}