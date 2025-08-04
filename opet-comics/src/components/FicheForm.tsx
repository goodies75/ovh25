import { useState } from "react";

export default function FicheForm() {
  const [fiche, setFiche] = useState({
    titre: "",
    description: "",
    image_url: ""
  });

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    try {
      const response = await fetch("/api/post-fiche.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(fiche),
      });

      const result = await response.json();
      console.log("Résultat API :", result);

      if (result.success) {
        alert("Fiche ajoutée !");
        setFiche({ titre: "", description: "", image_url: "" });
        // Déclencher un événement pour rafraîchir la liste
        window.dispatchEvent(new CustomEvent('ficheAdded'));
      } else {
        alert("Erreur : " + (result.error || "Erreur inconnue"));
      }
    } catch (error) {
      console.error("Erreur lors de l'ajout :", error);
      alert("Erreur de connexion");
    }
  };

  return (
    <div className="form-container">
      <h2>📝 Ajouter une fiche</h2>
      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <input
            className="form-input"
            placeholder="Titre"
            value={fiche.titre}
            onChange={(e) => setFiche({ ...fiche, titre: e.target.value })}
            required
          />
        </div>
        <div className="form-group">
          <textarea
            className="form-textarea"
            placeholder="Description"
            value={fiche.description}
            onChange={(e) => setFiche({ ...fiche, description: e.target.value })}
            required
          />
        </div>
        <div className="form-group">
          <input
            className="form-input"
            placeholder="Image URL (optionnel)"
            value={fiche.image_url}
            onChange={(e) => setFiche({ ...fiche, image_url: e.target.value })}
          />
        </div>
        <button type="submit" className="btn">
          ➕ Ajouter
        </button>
      </form>
    </div>
  );
}
