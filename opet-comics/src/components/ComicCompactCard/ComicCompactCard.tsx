import { Card } from '../ui';
import './ComicCompactCard.css';

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

interface ComicCompactCardProps {
  fiche: Fiche;
  onClick: (fiche: Fiche) => void;
}

export default function ComicCompactCard({ fiche, onClick }: ComicCompactCardProps) {
  const handleClick = () => {
    onClick(fiche);
  };

  return (
    <Card className="comic-compact-card" onClick={handleClick}>
      <div className="compact-image-container">
        {fiche.image_url ? (
          <img
            src={fiche.image_url}
            alt={`${fiche.nom_serie} ${fiche.numero}`}
            className="compact-image"
            onError={(e) => {
              (e.target as HTMLImageElement).src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA2MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMCAyMEg0MFY2MEgyMFYyMFoiIGZpbGw9IiNENURCREIiLz4KPHN2ZyB4PSIyNSIgeT0iNjUiIHdpZHRoPSIxMCIgaGVpZ2h0PSIxMCIgZmlsbD0iIzlDQTRBRiI+CjxwYXRoIGQ9Ik0wIDBoMTB2MTBIMHoiLz4KPC9zdmc+CjwvZGVmcz4KPC9zdmc+';
            }}
          />
        ) : (
          <div className="compact-placeholder">
            <span className="compact-placeholder-icon">📚</span>
          </div>
        )}
      </div>
      
      <div className="compact-info">
        <div className="compact-header">
          <h3 className="compact-title">
            {fiche.nom_serie}
            {fiche.numero && <span className="compact-numero">#{fiche.numero}</span>}
          </h3>
          {fiche.titre_secondaire && (
            <p className="compact-subtitle">{fiche.titre_secondaire}</p>
          )}
        </div>
        
        <div className="compact-details">
          <div className="compact-publisher">
            <span className="compact-icon">📚</span>
            <span className="compact-text">{fiche.editeur || 'Éditeur non renseigné'}</span>
          </div>
          
          {fiche.annee && (
            <div className="compact-year">
              <span className="compact-icon">📅</span>
              <span className="compact-text">{fiche.annee}</span>
            </div>
          )}
          
          <div className="compact-state">
            <span className="compact-icon">⭐</span>
            <span className="compact-text">{fiche.etat}</span>
          </div>
        </div>
        
        <div className="compact-click-hint">
          <span>👆 Cliquer pour voir les détails</span>
        </div>
      </div>
    </Card>
  );
}
