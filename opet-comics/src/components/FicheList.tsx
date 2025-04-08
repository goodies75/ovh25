import { useEffect, useState } from "react";

interface Fiche {
  id: number;
  titre: string;
  description: string;
  image_url?: string;
  created_at: string;
}

export default function FicheList() {
  const [fiches, setFiches] = useState<Fiche[]>([]);

  useEffect(() => {
    fetch("https://o-petit.com/profil/api/get-fiches.php")
      .then((res) => res.json())
      .then((data) => setFiches(data))
      .catch((err) => console.error("Erreur lors du fetch :", err));
  }, []);

  return (
    <div>
      <h2>📚 Liste des Comics</h2>
      {fiches.length === 0 && <p>Aucune fiche disponible</p>}
      <ul>
        {fiches.map((fiche) => (
          <li key={fiche.id}>
            <strong>{fiche.titre}</strong> — {fiche.description}
            {fiche.image_url && (
              <div>
                <img
                  src={fiche.image_url}
                  alt={fiche.titre}
                  width="150"
                  style={{ marginTop: "10px" }}
                />
              </div>
            )}
          </li>
        ))}
      </ul>
    </div>
  );
}
