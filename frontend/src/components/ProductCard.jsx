export default function ProductCard({ product }) {
  return (
    <div className="bg-neutral-800 rounded-lg p-4 shadow hover:scale-105 transition">
      <img
        src={product.image}
        alt={product.name}
        className="rounded-lg mb-3 w-full h-48 object-cover"
      />
      <h3 className="text-lg font-semibold">{product.name}</h3>
      <p className="text-lime-400 font-bold">${product.price.toLocaleString()}</p>
      <button
        className="mt-2 w-full bg-lime-500 text-black py-2 rounded-md hover:bg-lime-400"
        onClick={() => {

          console.log("Agregar al carrito:", product.name);
        }}
      >
        Agregar al carrito
      </button>
    </div>
  );
}
