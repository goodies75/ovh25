import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      // APIs PHP vers le serveur PHP local
      '/get-fiches.php': 'http://localhost:8080',
      '/post-fiche.php': 'http://localhost:8080',
      '/update-fiche.php': 'http://localhost:8080',
      '/delete-fiche.php': 'http://localhost:8080',
      '/upload-image.php': 'http://localhost:8080',
      '/uploads': 'http://localhost:8080'
    }
  }
})
