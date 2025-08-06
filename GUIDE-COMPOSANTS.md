# 🎨 Guide d'Utilisation - Nouveaux Composants UI

## 📁 Structure des Composants

```
src/
├── components/
│   ├── ui/                     # Composants réutilisables
│   │   ├── Button.tsx/.css
│   │   ├── Card.tsx/.css
│   │   ├── Input.tsx/.css
│   │   ├── Textarea.tsx/.css
│   │   ├── Select.tsx/.css
│   │   └── index.ts           # Export centralisé
│   └── Modal/
│       ├── Modal.tsx/.css
└── styles/
    └── variables.css          # Variables CSS globales
```

## 🎯 Variables CSS Disponibles

### Couleurs
```css
--color-primary-start: #667eea
--color-primary-end: #764ba2
--color-primary-gradient: linear-gradient(...)
--color-white: #ffffff
--color-text-primary: #333333
--color-text-secondary: #666666
--color-error: #dc3545
--color-success: #28a745
```

### Espacements
```css
--spacing-xs: 5px
--spacing-sm: 10px
--spacing-md: 15px
--spacing-lg: 20px
--spacing-xl: 30px
--spacing-xxl: 40px
```

### Rayons de bordure
```css
--border-radius-sm: 8px
--border-radius-md: 12px
--border-radius-lg: 15px
```

## 🧩 Utilisation des Composants

### Button
```tsx
import { Button } from './components/ui';

<Button variant="primary" onClick={handleClick}>
  Ajouter
</Button>

<Button variant="danger" onClick={handleDelete}>
  Supprimer
</Button>

<Button variant="delete" className="delete-btn">
  ✕
</Button>
```

### Input
```tsx
import { Input } from './components/ui';

<Input
  label="Nom de série"
  value={nomSerie}
  onChange={setNomSerie}
  placeholder="Ex: Batman"
  required
/>
```

### Textarea
```tsx
import { Textarea } from './components/ui';

<Textarea
  label="Description"
  value={description}
  onChange={setDescription}
  rows={4}
  placeholder="Décrivez le comic..."
/>
```

### Select
```tsx
import { Select } from './components/ui';

const etatOptions = [
  { value: "neuf", label: "Neuf" },
  { value: "tres-bon", label: "Très bon" },
  { value: "bon", label: "Bon" }
];

<Select
  label="État"
  value={etat}
  onChange={setEtat}
  options={etatOptions}
  placeholder="Choisir un état"
/>
```

### Card
```tsx
import { Card } from './components/ui';

<Card className="comic-card" hover>
  <div className="comic-content">
    {/* Contenu de la carte */}
  </div>
</Card>
```

### Modal
```tsx
import Modal from './components/Modal/Modal';
import { Button } from './components/ui';

<Modal
  isOpen={showModal}
  onClose={() => setShowModal(false)}
  title="Confirmer la suppression"
  actions={
    <>
      <Button variant="danger" onClick={handleConfirm}>
        Confirmer
      </Button>
      <Button variant="cancel" onClick={() => setShowModal(false)}>
        Annuler
      </Button>
    </>
  }
>
  <p>Êtes-vous sûr de vouloir supprimer ce comic ?</p>
</Modal>
```

## 🎨 Personnalisation des Couleurs

Pour modifier les couleurs, éditez le fichier `src/styles/variables.css` :

```css
:root {
  /* Exemple: Thème rouge */
  --color-primary-start: #e74c3c;
  --color-primary-end: #c0392b;
  
  /* Exemple: Thème vert */
  --color-primary-start: #27ae60;
  --color-primary-end: #229954;
  
  /* Exemple: Thème orange */
  --color-primary-start: #f39c12;
  --color-primary-end: #e67e22;
}
```

## 🔄 Migration des Composants Existants

### Avant (HTML/CSS classique)
```tsx
<button className="btn" onClick={onClick}>
  Texte
</button>

<input 
  className="form-input" 
  value={value} 
  onChange={e => setValue(e.target.value)}
/>
```

### Après (Composants modulaires)
```tsx
import { Button, Input } from './components/ui';

<Button variant="primary" onClick={onClick}>
  Texte
</Button>

<Input 
  value={value} 
  onChange={setValue}
  label="Libellé"
/>
```

## ✨ Avantages de cette Architecture

1. **🔧 Réutilisabilité** : Composants utilisables partout
2. **🎨 Cohérence** : Design uniforme via les variables CSS
3. **📱 Accessibilité** : Attributs ARIA et bonnes pratiques
4. **🚀 Performance** : CSS modulaire, chargement optimisé
5. **🛠️ Maintenance** : Code organisé et facile à modifier
6. **🎯 Types** : Support TypeScript complet
