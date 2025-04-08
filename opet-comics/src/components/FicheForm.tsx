import { useState } from "react";

export default function FicheForm() {
  const [fiche, setFiche] = useState({
    titre: "",
    description: "",
    image_url: ""
  });

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    const response = await fetch("https://o-petit.com/profil/api/post-fiche.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(fiche),
    });

    const result = await response.json();
    console.log("Résultat API :", result);

    if (result.success) {
      alert("Fiche ajoutée !");
      setFiche({ titre: "", description: "", image_url: "" });
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <input
        placeholder="Titre"
        value={fiche.titre}
        onChange={(e) => setFiche({ ...fiche, titre: e.target.value })}
      />
      <input
        placeholder="Description"
        value={fiche.description}
        onChange={(e) => setFiche({ ...fiche, description: e.target.value })}
      />
      <input
        placeholder="Image URL"
        value={fiche.image_url}
        onChange={(e) => setFiche({ ...fiche, image_url: e.target.value })}
      />
      <button type="submit">Ajouter</button>
    </form>
  );
}
