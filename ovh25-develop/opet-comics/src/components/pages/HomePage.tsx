import { Link } from 'react-router-dom';
import { Card } from '../ui';
import './HomePage.css';

interface HomePageProps {
  statsData?: {
    totalComics: number;
    lastAdded?: string;
  };
}

export default function HomePage({ statsData }: HomePageProps) {
  return (
    <div className="home-page">
      <div className="hero-section">
        <div className="hero-content">
          <h2>🎨 Bienvenue dans Opet Comics</h2>
          <p className="hero-description">
            Gérez votre collection de bandes dessinées et comics avec style.
            Ajoutez, organisez et consultez vos œuvres préférées en toute simplicité.
          </p>
        </div>
      </div>

      <div className="quick-actions">
        <Card className="action-card">
          <div className="action-content">
            <div className="action-icon">➕</div>
            <h3>Ajouter un Comic</h3>
            <p>Enrichissez votre collection en ajoutant de nouveaux comics avec toutes leurs informations détaillées.</p>
            <Link to="/add" className="btn btn--full-width">
              Ajouter maintenant
            </Link>
          </div>
        </Card>

        <Card className="action-card">
          <div className="action-content">
            <div className="action-icon">📚</div>
            <h3>Consulter la Liste</h3>
            <p>Parcourez votre collection, triez par ordre alphabétique et gérez vos comics existants.</p>
            <Link to="/list" className="btn btn--full-width">
              Voir la collection
            </Link>
          </div>
        </Card>
      </div>

      {statsData && (
        <div className="stats-section">
          <Card className="stats-card">
            <h3>📊 Statistiques de votre collection</h3>
            <div className="stats-grid">
              <div className="stat-item">
                <span className="stat-number">{statsData.totalComics}</span>
                <span className="stat-label">Comics au total</span>
              </div>
              {statsData.lastAdded && (
                <div className="stat-item">
                  <span className="stat-text">{statsData.lastAdded}</span>
                  <span className="stat-label">Dernier ajout</span>
                </div>
              )}
            </div>
          </Card>
        </div>
      )}

      <div className="features-section">
        <h3>✨ Fonctionnalités</h3>
        <div className="features-grid">
          <div className="feature-item">
            <span className="feature-icon">🎨</span>
            <h4>Interface moderne</h4>
            <p>Design épuré et responsive pour tous vos appareils</p>
          </div>
          <div className="feature-item">
            <span className="feature-icon">🔍</span>
            <h4>Tri intelligent</h4>
            <p>Organisez votre collection par ordre alphabétique</p>
          </div>
          <div className="feature-item">
            <span className="feature-icon">📱</span>
            <h4>Responsive</h4>
            <p>Utilisable sur mobile, tablette et ordinateur</p>
          </div>
          <div className="feature-item">
            <span className="feature-icon">💾</span>
            <h4>Sauvegarde automatique</h4>
            <p>Vos données sont automatiquement sauvegardées</p>
          </div>
        </div>
      </div>
    </div>
  );
}
