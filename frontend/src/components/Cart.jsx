import { motion, AnimatePresence } from "framer-motion";
import { X, Plus, Minus, ShoppingCart } from "lucide-react";

export default function Cart({ isOpen, onClose, cart, onRemoveItem, onUpdateQuantity, total }) {
  
  const formatPrice = (price) => {
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP'
    }).format(price);
  };

  console.log(cart);
  
  
  return (
    <AnimatePresence>
      {isOpen && (
        <>
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black/50 z-40"
            onClick={onClose}
          />
          
          <motion.div
            initial={{ x: '100%' }}
            animate={{ x: 0 }}
            exit={{ x: '100%' }}
            transition={{ type: 'spring', damping: 30 }}
            className="fixed right-0 top-0 h-full w-full max-w-md bg-neutral-900 shadow-2xl z-50 flex flex-col"
          >
            <div className="flex items-center justify-between p-4 border-b border-neutral-700">
              <div className="flex items-center gap-3">
                <ShoppingCart className="text-lime-500" size={24} />
                <h2 className="text-xl font-bold text-white">Tu Carrito</h2>
                <span className="bg-lime-600 text-black px-2 py-1 rounded-full text-sm font-medium">
                  {cart.resumen.total_items} items
                </span>
              </div>
              <button
                onClick={onClose}
                className="p-2 hover:bg-neutral-800 rounded-lg transition"
              >
                <X className="text-white" size={24} />
              </button>
            </div>

            <div className="flex-1 overflow-y-auto p-4">
              {cart.length === 0 ? (
                <div className="text-center py-12">
                  <ShoppingCart className="mx-auto text-neutral-600 mb-4" size={64} />
                  <p className="text-neutral-400 text-lg">Tu carrito está vacío</p>
                  <p className="text-neutral-500 text-sm mt-2">Agrega algunos productos para continuar</p>
                </div>
              ) : (
                <div className="space-y-4">
                  {cart.detalle.map((item) => {
                    const imgSrc = `/src/assets/${item.imagen_Producto}`;

                    return (
                      <div key={item.id} className="flex gap-4 p-4 bg-neutral-800 rounded-lg">
                        <img
                          src={imgSrc}
                          alt={item.nombre_Producto}
                          className="w-20 h-20 object-cover rounded-lg"
                          onError={(e) => {
                            e.target.onerror = null;
                            e.target.src = "/src/assets/default.png";
                          }}
                        />

                        <div className="flex-1">
                          <h3 className="font-semibold text-white">{item.nombre_Producto}</h3>
                          <p className="text-lime-500 font-bold">{formatPrice(item.subtotal)}</p>

                          <div className="flex items-center gap-3 mt-2">
                            <button
                              onClick={() => onUpdateQuantity(item.id_Producto, item.cantidad - 1)}
                              className="p-1 bg-neutral-700 rounded hover:bg-neutral-600 transition"
                            >
                              <Minus size={16} />
                            </button>

                            <span className="text-white font-medium w-8 text-center">{item.cantidad}</span>

                            <button
                              onClick={() => onUpdateQuantity(item.id_Producto, item.cantidad + 1)}
                              className="p-1 bg-neutral-700 rounded hover:bg-neutral-600 transition"
                            >
                              <Plus size={16} />
                            </button>

                            <button
                              onClick={() => onRemoveItem(item.id_Producto)}
                              className="ml-auto text-red-400 hover:text-red-300 transition text-sm"
                            >
                              Eliminar
                            </button>
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>
              )}
            </div>

            {cart.length === 0 ? (
                <div className="text-center py-12">
                  <ShoppingCart className="mx-auto text-neutral-600 mb-4" size={64} />
                </div>
              ) : (
              <div className="border-t border-neutral-700 p-4 space-y-4">
                <div className="flex justify-between items-center text-lg border-t border-neutral-600 pt-2">
                  <span className="text-white font-bold">Total:</span>
                  <span className="text-lime-500 font-bold">{formatPrice(cart.resumen.valor_total)}</span>
                </div>
                <button onClick={() => { alert('Proceso de pago en desarrollo'); onClose(); }} className="w-full bg-lime-600 hover:bg-lime-500 text-black font-bold py-3 rounded-lg transition duration-200">
                  Proceder al Pago
                </button>
                <button onClick={onClose} className="w-full border border-neutral-600 text-white hover:bg-neutral-800 font-medium py-3 rounded-lg transition">
                  Continuar Comprando
                </button>
              </div>
            )}
          </motion.div>
        </>
      )}
    </AnimatePresence>
  );
}