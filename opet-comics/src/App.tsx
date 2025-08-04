
import './App.css'
import FicheForm from './components/FicheForm';
import FicheList from './components/FicheList';
// import ComicFormApp from './components/ComicFormApp'



function App() {
  return (
    <main>
      <div className="container">
        <header className="header">
          <h1>🎨 Opet Comics</h1>
          <p>Gérez votre collection de fiches comics</p>
        </header>
        
        <FicheForm />
        <FicheList />
      </div>
    </main>
  )
}

export default App
