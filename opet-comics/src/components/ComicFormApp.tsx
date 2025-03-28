import { useState, ChangeEvent, FormEvent } from "react";

type Comic = {
  id_comic: number;
  titre: string;
  auteur: string;
  editeur: string;
  edition: string;
  annee: string;
  remarques: string;
  exemplaires: Exemplaire[];
};

type Exemplaire = {
  id_exemplaire: number;
  etat: string;
  localisation: string;
  remarque: string;
};

export default function ComicFormApp() {
  const [comics, setComics] = useState<Comic[]>([]);
  const [form, setForm] = useState<Omit<Comic, "id_comic" | "exemplaires">>({
    titre: "",
    auteur: "",
    editeur: "",
    edition: "",
    annee: "",
    remarques: ""
  });

  const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const newComic: Comic = {
      id_comic: comics.length + 1,
      ...form,
      exemplaires: []
    };
    setComics([...comics, newComic]);
    setForm({
      titre: "",
      auteur: "",
      editeur: "",
      edition: "",
      annee: "",
      remarques: ""
    });
  };
  const handleExportJSON = () => {
    const json = JSON.stringify(comics, null, 2);
    const blob = new Blob([json], { type: "application/json" });
    const url = URL.createObjectURL(blob);

    const link = document.createElement("a");
    link.href = url;
    link.download = "ma_collection_comics.json";
    link.click();

    URL.revokeObjectURL(url);
  };
  return (
    <div className="p-6 max-w-xl mx-auto">
      <h2 className="text-2xl font-bold mb-4">Ajouter un Comic</h2>
      <form onSubmit={handleSubmit} className="space-y-3">
        {(Object.keys(form) as (keyof typeof form)[]).map((field) => (
          <input
            key={field}
            type="text"
            name={field}
            placeholder={field}
            value={form[field]}
            onChange={handleChange}
            className="w-full border p-2 rounded"
          />
        ))}
        <button type="submit" className="bg-blue-600 text-white px-4 py-2 rounded">
          Ajouter
        </button>
      </form>

      <h3 className="mt-6 text-xl font-semibold">Liste des Comics</h3>
      <ul className="list-disc list-inside mt-2">
        {comics.map((comic) => (
          <li key={comic.id_comic}>
            <strong>{comic.titre}</strong> ({comic.annee}) - {comic.editeur}
          </li>
        ))}
      </ul>
      <button
        type="button"
        onClick={handleExportJSON}
        className="bg-green-600 text-white px-4 py-2 rounded"
      >

      </button>

    </div>
  );
}
