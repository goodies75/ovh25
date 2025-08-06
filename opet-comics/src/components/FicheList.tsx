import { useEffect, useState } from "react";
import { Button, Card } from "./ui";
import Modal from "./Modal/Modal";

interface Fiche {
  id: number;
  nom_serie: string;
  numero: string;
  annee: string;
  numero_edition: string;
  editeur: string;
  auteur_couverture: string;
  autres_auteurs: string[];
  titre_secondaire: string;
  etat: string;
  isbn: string;
  description: string;
  image_url?: string;
  created_at: string;
}

export default function FicheList() {
  const [fiches, setFiches] = useState<Fiche[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [ficheToDelete, setFicheToDelete] = useState<Fiche | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);

  const fetchFiches = async () => {
    try {
      setLoading(true);
      setError(null);
      // Utiliser le chemin relatif pour le déploiement
      const response = await fetch("./get-fiches.php");
      if (!response.ok) {
        throw new Error(`Erreur HTTP: ${response.status}`);
      }
      const data = await response.json();

      // Normaliser les données pour être compatibles avec l'ancien et le nouveau format
      const normalizedFiches = data.map((fiche: any) => ({
        id: fiche.id,
        nom_serie: fiche.nom_serie || fiche.titre || "Sans titre",
        numero: fiche.numero || "",
        annee: fiche.annee || "",
        numero_edition: fiche.numero_edition || "",
        editeur: fiche.editeur || "",
        auteur_couverture: fiche.auteur_couverture || "",
        autres_auteurs: Array.isArray(fiche.autres_auteurs) ? fiche.autres_auteurs : [],
        titre_secondaire: fiche.titre_secondaire || "",
        etat: fiche.etat || "Non défini",
        isbn: fiche.isbn || "",
        description: fiche.description || "",
        image_url: fiche.image_url || "",
        created_at: fiche.created_at
      }));

      setFiches(normalizedFiches);
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

  const handleDeleteClick = (fiche: Fiche) => {
    setFicheToDelete(fiche);
  };

  const handleDeleteConfirm = async () => {
    if (!ficheToDelete) return;

    try {
      setIsDeleting(true);

      const response = await fetch('./delete-fiche.php', {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: ficheToDelete.id })
      });

      if (response.ok) {
        // Supprimer la fiche de l'état local
        setFiches(fiches.filter(f => f.id !== ficheToDelete.id));
        setFicheToDelete(null);

        // Optionnel : afficher un message de succès
        console.log('Fiche supprimée avec succès');
      } else {
        throw new Error('Erreur lors de la suppression');
      }
    } catch (err) {
      console.error('Erreur lors de la suppression :', err);
      setError('Impossible de supprimer la fiche');
    } finally {
      setIsDeleting(false);
    }
  };

  const handleDeleteCancel = () => {
    setFicheToDelete(null);
  };

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
            <Card key={fiche.id} className="comic-card">
              <Button
                variant="delete"
                onClick={() => handleDeleteClick(fiche)}
                title="Supprimer cette fiche"
              >
                ✕
              </Button>

              <div className="comic-content">
                {/* Informations à gauche */}
                <div className="comic-info">
                  <div className="comic-header">
                    <h3 className="comic-serie">{fiche.nom_serie}</h3>
                    {fiche.numero && <span className="comic-numero">#{fiche.numero}</span>}
                  </div>

                  {fiche.titre_secondaire && (
                    <h4 className="comic-titre-secondaire">{fiche.titre_secondaire}</h4>
                  )}

                  <div className="comic-details">
                    {fiche.editeur && <p className="comic-editeur">📚 {fiche.editeur}</p>}
                    {fiche.annee && <p className="comic-annee">📅 {fiche.annee}</p>}
                    {fiche.numero_edition && <p className="comic-edition">🔖 Édition #{fiche.numero_edition}</p>}

                    {fiche.auteur_couverture && (
                      <p className="comic-auteur">🎨 Couverture: {fiche.auteur_couverture}</p>
                    )}

                    {fiche.autres_auteurs && fiche.autres_auteurs.length > 0 && (
                      <p className="comic-auteurs">✍️ {fiche.autres_auteurs.join(', ')}</p>
                    )}

                    <p className="comic-etat">⭐ État: {fiche.etat}</p>

                    {fiche.isbn && <p className="comic-isbn">📖 ISBN: {fiche.isbn}</p>}
                  </div>

                  {fiche.description && (
                    <p className="comic-description">{fiche.description}</p>
                  )}

                  <p className="card-date">
                    Ajouté le {new Date(fiche.created_at).toLocaleDateString('fr-FR')}
                  </p>
                </div>

                {/* Image à droite */}
                <div className="comic-image-container">
                  {fiche.image_url ? (
                    <img
                      src={fiche.image_url}
                      alt={`${fiche.nom_serie} ${fiche.numero}`}
                      className="comic-image"
                      onError={(e) => {
                        (e.target as HTMLImageElement).src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDIwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik04MCA4MEgxMjBWMjIwSDgwVjgwWiIgZmlsbD0iI0Q1REJEQiIvPgo8cGF0aCBkPSJNOTAgOTBIMTEwVjIxMEg5MFY5MFoiIGZpbGw9IiM5Q0E0QUYiLz4KPHRleHQgeD0iMTAwIiB5PSIyNDAiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZpbGw9IiM5Q0E0QUYiIGZvbnQtc2l6ZT0iMTIiPkNvbWljPC90ZXh0Pgo8L3N2Zz4=';
                        (e.target as HTMLImageElement).style.display = 'block';
                      }}
                    />
                  ) : (
                    <div className="comic-placeholder">
                      <div className="placeholder-icon">📚</div>
                      <div className="placeholder-text">Pas d'image</div>
                    </div>
                  )}
                </div>
              </div>
            </Card>
          ))}
        </div>
      )}

      {/* Popup de confirmation de suppression */}
      <Modal
        isOpen={!!ficheToDelete}
        onClose={handleDeleteCancel}
        title="Confirmer la suppression"
        actions={
          <>
            <Button
              variant="danger"
              onClick={handleDeleteConfirm}
              disabled={isDeleting}
            >
              {isDeleting ? 'Suppression...' : 'Oui, supprimer'}
            </Button>
            <Button
              variant="cancel"
              onClick={handleDeleteCancel}
              disabled={isDeleting}
            >
              Annuler
            </Button>
          </>
        }
      >
        <p>Êtes-vous sûr de vouloir supprimer le comic "<strong>{ficheToDelete?.nom_serie} {ficheToDelete?.numero}</strong>" ?</p>
        <p className="modal-warning">Cette action est irréversible.</p>
      </Modal>
    </div>
  );
}
