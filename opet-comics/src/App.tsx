
import './App.css'
import FicheForm from './components/FicheForm';
import FicheList from './components/FicheList';
// import ComicFormApp from './components/ComicFormApp'



function App() {


  return (
    <main className="min-h-screen bg-gray-100 p-6">
      <FicheForm />
      <hr />
      <FicheList />
    </main>
  )
}

export default App
