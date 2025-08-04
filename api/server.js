import express from 'express';
import cors from 'cors';
import fs from 'fs/promises';
import path from 'path';

const app = express();
const PORT = 3001;

// Middleware
app.use(cors());
app.use(express.json());

const FICHES_FILE = './fiches.json';

// Utilitaire pour lire les fiches
async function readFiches() {
  try {
    const data = await fs.readFile(FICHES_FILE, 'utf8');
    return JSON.parse(data);
  } catch (error) {
    return [];
  }
}

// Utilitaire pour écrire les fiches
async function writeFiches(fiches) {
  await fs.writeFile(FICHES_FILE, JSON.stringify(fiches, null, 2));
}

// Route GET pour récupérer les fiches
app.get('/get-fiches.php', async (req, res) => {
  try {
    const fiches = await readFiches();
    // Trier par date de création (plus récent en premier)
    fiches.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    res.json(fiches);
  } catch (error) {
    console.error('Erreur lors de la lecture des fiches:', error);
    res.status(500).json({ error: 'Erreur serveur' });
  }
});

// Route POST pour ajouter une fiche
app.post('/post-fiche.php', async (req, res) => {
  try {
    const { titre, description, image_url } = req.body;

    // Validation
    if (!titre || !description) {
      return res.status(400).json({ error: 'Titre et description requis' });
    }

    const fiches = await readFiches();
    
    const newFiche = {
      id: Date.now(),
      titre,
      description,
      image_url: image_url || '',
      created_at: new Date().toISOString()
    };

    fiches.push(newFiche);
    await writeFiches(fiches);

    res.json({
      success: true,
      message: 'Fiche ajoutée avec succès',
      data: newFiche
    });
  } catch (error) {
    console.error('Erreur lors de l\'ajout de la fiche:', error);
    res.status(500).json({ error: 'Erreur serveur' });
  }
});

app.listen(PORT, () => {
  console.log(`🚀 Serveur API démarré sur http://localhost:${PORT}`);
});
