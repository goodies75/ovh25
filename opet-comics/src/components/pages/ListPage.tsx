import { useEffect, useState } from "react";
import { Button } from "../ui";
import ComicCompactCard from '../ComicCompactCard';
import ComicDetailModal from '../ComicDetailModal';
import ComicEditModal from '../ComicEditModal';
import PinProtection from '../security/PinProtection';
import { useAdminAuth } from '../../hooks/useAdminAuth';
import Modal from '../Modal/Modal';

interface Fiche {
  id: number;
  nom_serie: string;
  titre?: string;
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

type SortOption = 'nom_serie' | 'annee' | 'editeur' | 'date_added';
type SortDirection = 'asc' | 'desc';

export default function ListPage() {
  const [fiches, setFiches] = useState<Fiche[]>([]);
  const [sortedFiches, setSortedFiches] = useState<Fiche[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const { isAuthorized, authorize } = useAdminAuth();

  const [selectedFiche, setSelectedFiche] = useState<Fiche | null>(null);
  const [showDetailModal, setShowDetailModal] = useState(false);
  const [showEditModal, setShowEditModal] = useState(false);
  const [ficheToDelete, setFicheToDelete] = useState<Fiche | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);

  const [showPinModal, setShowPinModal] = useState(false);
  const [pendingAction, setPendingAction] = useState<'edit' | 'delete' | null>(null);
  const [pendingFiche, setPendingFiche] = useState<Fiche | null>(null);

  const [sortBy, setSortBy] = useState<SortOption>('nom_serie');
  const [sortDirection, setSortDirection] = useState<SortDirection>('asc');
  const [searchTerm, setSearchTerm] = useState('');

  const fetchFiches = async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await fetch("./get-fiches.php");
      if (!response.ok) {
        throw new Error(`Erreur HTTP: ${response.status}`);
      }
      const data = await response.json();

      const normalizedFiches = data.map((fiche: any) => ({
        ...fiche,
        id: fiche.id,
        nom_serie: fiche.nom_serie || fiche.titre || "Sans titre",
        titre: fiche.titre || fiche.nom_serie || "Sans titre",
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

  const sortFiches = (fiches: Fiche[], sortBy: SortOption, direction: SortDirection, searchTerm: string) => {
    let filtered = fiches;

    if (searchTerm) {
      filtered = fiches.filter(fiche =>
        fiche.nom_serie.toLowerCase().includes(searchTerm.toLowerCase()) ||
        fiche.editeur.toLowerCase().includes(searchTerm.toLowerCase()) ||
        fiche.auteur_couverture.toLowerCase().includes(searchTerm.toLowerCase()) ||
        fiche.autres_auteurs.some(auteur => auteur.toLowerCase().includes(searchTerm.toLowerCase()))
      );
    }

    const sorted = [...filtered].sort((a, b) => {
      let valueA: string | number = '';
      let valueB: string | number = '';

      switch (sortBy) {
        case 'nom_serie':
          valueA = a.nom_serie.toLowerCase();
          valueB = b.nom_serie.toLowerCase();
          break;
        case 'annee':
          valueA = parseInt(a.annee) || 0;
          valueB = parseInt(b.annee) || 0;
          break;
        case 'editeur':
          valueA = a.editeur.toLowerCase();
          valueB = b.editeur.toLowerCase();
          break;
        case 'date_added':
          valueA = new Date(a.created_at).getTime();
          valueB = new Date(b.created_at).getTime();
          break;
      }

      if (direction === 'asc') {
        return valueA < valueB ? -1 : valueA > valueB ? 1 : 0;
      } else {
        return valueA > valueB ? -1 : valueA < valueB ? 1 : 0;
      }
    });

    return sorted;
  };

  useEffect(() => {
    const sorted = sortFiches(fiches, sortBy, sortDirection, searchTerm);
    setSortedFiches(sorted);
  }, [fiches, sortBy, sortDirection, searchTerm]);

  useEffect(() => {
    fetchFiches();
  }, []);

  const requireAuth = (action: 'edit' | 'delete', fiche: Fiche) => {
    if (isAuthorized) {
      if (action === 'edit') {
        openEditModal(fiche);
      } else {
        setFicheToDelete(fiche);
      }
    } else {
      setPendingAction(action);
      setPendingFiche(fiche);
      setShowPinModal(true);
    }
  };

  const handlePinSuccess = () => {
    authorize();
    setShowPinModal(false);

    if (pendingAction === 'edit' && pendingFiche) {
      openEditModal(pendingFiche);
    } else if (pendingAction === 'delete' && pendingFiche) {
      setFicheToDelete(pendingFiche);
    }

    setPendingAction(null);
    setPendingFiche(null);
  };

  const handlePinCancel = () => {
    setShowPinModal(false);
    setPendingAction(null);
    setPendingFiche(null);
  };

  const openEditModal = (fiche: Fiche) => {
    setSelectedFiche(fiche);
    setShowEditModal(true);
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
        setFiches(fiches.filter(f => f.id !== ficheToDelete.id));
        setFicheToDelete(null);
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

  const handleCloseEditModal = () => {
    setShowEditModal(false);
    setSelectedFiche(null);
  };

  const handleSaveEdit = async (updatedFiche: Fiche) => {
    try {
      console.log('Sauvegarde de:', updatedFiche);

      const response = await fetch('./update-fiche.php', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(updatedFiche),
      });

      const result = await response.json();

      if (result.success) {
        setFiches(fiches.map(f => f.id === updatedFiche.id ? updatedFiche : f));
        setShowEditModal(false);
        alert('Comic mis à jour avec succès !');
        fetchFiches();
      } else {
        throw new Error(result.error || 'Erreur lors de la sauvegarde');
      }

    } catch (error) {
      console.error('Erreur lors de la sauvegarde:', error);
      alert('Erreur lors de la sauvegarde: ' + (error as Error).message);
    }
  };

  const handleSortChange = (newSortBy: SortOption) => {
    if (newSortBy === sortBy) {
      setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
    } else {
      setSortBy(newSortBy);
      setSortDirection('asc');
    }
  };

  return (
    <div className="min-h-screen py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="text-center mb-12">
          <h2 className="text-4xl sm:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-primary-600 to-teal-600 bg-clip-text text-transparent">
            📚 Collection de Comics
          </h2>
          <p className="text-lg text-dark-600">Explorez et gérez votre collection complète</p>
        </div>

        {/* Contrôles */}
        <div className="mb-8 space-y-6">
          {/* Recherche */}
          <div className="max-w-2xl mx-auto">
            <input
              type="text"
              placeholder="🔍 Rechercher par titre, éditeur, auteur..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full px-6 py-4 rounded-xl border-2 border-dark-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-all duration-300 text-lg shadow-lg"
            />
          </div>

          {/* Tri */}
          <div className="flex flex-wrap items-center justify-center gap-3">
            <span className="text-sm font-semibold text-dark-700">Trier par :</span>
            {[
              { key: 'nom_serie' as SortOption, label: 'Titre' },
              { key: 'annee' as SortOption, label: 'Année' },
              { key: 'editeur' as SortOption, label: 'Éditeur' },
              { key: 'date_added' as SortOption, label: 'Date d\'ajout' },
            ].map((option) => (
              <button
                key={option.key}
                className={`
                  px-4 py-2 rounded-lg font-medium transition-all duration-300
                  transform hover:scale-105 active:scale-95
                  ${sortBy === option.key
                    ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg'
                    : 'bg-white text-dark-700 border-2 border-dark-200 hover:border-primary-300'
                  }
                `}
                onClick={() => handleSortChange(option.key)}
              >
                {option.label} {sortBy === option.key && (sortDirection === 'asc' ? '↑' : '↓')}
              </button>
            ))}
          </div>
        </div>

        {/* Résultats info */}
        {!loading && !error && (
          <div className="text-center mb-6 text-dark-600 font-medium">
            <span className="bg-white/60 backdrop-blur-sm px-4 py-2 rounded-full border border-dark-200 inline-block">
              {sortedFiches.length} comic(s) trouvé(s)
              {searchTerm && ` pour "${searchTerm}"`}
            </span>
          </div>
        )}

        {/* Loading */}
        {loading && (
          <div className="text-center py-20">
            <div className="spinner mx-auto mb-4"></div>
            <p className="text-dark-600 text-lg">Chargement de votre collection...</p>
          </div>
        )}

        {/* Error */}
        {error && (
          <div className="error max-w-2xl mx-auto">
            {error}
          </div>
        )}

        {/* Empty states */}
        {!loading && !error && sortedFiches.length === 0 && !searchTerm && (
          <div className="empty-state max-w-2xl mx-auto">
            <div className="text-6xl mb-4">📭</div>
            <p>Aucune fiche dans votre collection</p>
            <small>Commencez par ajouter votre premier comic !</small>
          </div>
        )}

        {!loading && !error && sortedFiches.length === 0 && searchTerm && (
          <div className="empty-state max-w-2xl mx-auto">
            <div className="text-6xl mb-4">🔍</div>
            <p>Aucun résultat pour "{searchTerm}"</p>
            <small>Essayez avec d'autres mots-clés</small>
          </div>
        )}

        {/* Liste de comics */}
        {!loading && !error && sortedFiches.length > 0 && (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            {sortedFiches.map((fiche) => (
              <ComicCompactCard
                key={fiche.id}
                fiche={fiche}
                onClick={() => {
                  setSelectedFiche(fiche);
                  setShowDetailModal(true);
                }}
              />
            ))}
          </div>
        )}

        {/* Modals */}
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
          <p className="text-dark-700 mb-2">Êtes-vous sûr de vouloir supprimer le comic "<strong>{ficheToDelete?.nom_serie} {ficheToDelete?.numero}</strong>" ?</p>
          <p className="text-danger-600 font-medium">⚠️ Cette action est irréversible.</p>
        </Modal>

        {showDetailModal && selectedFiche && (
          <ComicDetailModal
            isOpen={showDetailModal}
            onClose={() => setShowDetailModal(false)}
            fiche={selectedFiche}
            onEdit={() => {
              setShowDetailModal(false);
              requireAuth('edit', selectedFiche);
            }}
            onDelete={() => {
              setShowDetailModal(false);
              requireAuth('delete', selectedFiche);
            }}
          />
        )}

        {showEditModal && selectedFiche && (
          <ComicEditModal
            isOpen={showEditModal}
            fiche={selectedFiche}
            onClose={handleCloseEditModal}
            onSave={handleSaveEdit}
          />
        )}

        <PinProtection
          isOpen={showPinModal}
          onClose={handlePinCancel}
          onSuccess={handlePinSuccess}
          action={pendingAction === 'edit' ? 'modifier' : 'supprimer'}
          comicTitle={pendingFiche ? (pendingFiche.nom_serie || pendingFiche.titre) : undefined}
        />
      </div>
    </div>
  );
}
