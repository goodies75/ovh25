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
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchFiches = async () => {
    try {
      setLoading(true);
      setError(null);
      // Utiliser le chemin relatif pour o-petit.com
      const response = await fetch("./get-fiches.php");
      if (!response.ok) {
        throw new Error(`Erreur HTTP: ${response.status}`);
      }
      const data = await response.json();
      setFiches(data);
    } catch (err) {
      console.error("Erreur lors du fetch :", err);
      setError("Impossible de charger les fiches");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchFiches();

    // Écouter l'événement de nouvelle fiche ajoutée
    const handleFicheAdded = () => {
      fetchFiches();
    };

    window.addEventListener('ficheAdded', handleFicheAdded);
    return () => window.removeEventListener('ficheAdded', handleFicheAdded);
  }, []);

  return (
    <div className="cards-container">
      <h2>📚 Liste des Comics</h2>
      
      {loading && (
        <div className="loading">
          <div className="spinner"></div>
          <p>Chargement...</p>
        </div>
      )}
      
      {error && (
        <div className="error">
          {error}
        </div>
      )}
      
      {!loading && !error && fiches.length === 0 && (
        <div className="empty-state">
          <p>Aucune fiche disponible</p>
          <small>Commencez par ajouter votre première fiche !</small>
        </div>
      )}
      
      {!loading && !error && fiches.length > 0 && (
        <div className="cards-grid">
          {fiches.map((fiche) => (
            <div key={fiche.id} className="card">
              {fiche.image_url && (
                <img
                  src={fiche.image_url}
                  alt={fiche.titre}
                  className="card-image"
                  onError={(e) => {
                    (e.target as HTMLImageElement).style.display = 'none';
                  }}
                />
              )}
              <div className="card-content">
                <h3 className="card-title">{fiche.titre}</h3>
                <p className="card-description">{fiche.description}</p>
                <p className="card-date">
                  Ajouté le {new Date(fiche.created_at).toLocaleDateString('fr-FR')}
                </p>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
