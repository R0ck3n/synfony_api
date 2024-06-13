import { useEffect, useState } from "react";
import axios from "axios";

import "./App.css";

function App() {
    const url = "http://127.0.0.1:8000/list";
    const [ingredients, setIngredients] = useState([]);

    const loadData = async () => {
        const response = await axios.get(url);
        console.log(response.data);
        setIngredients(response.data);
    };

    useEffect(() => {
        loadData();
    }, []); // [] exécuté 1 fois au démarrage

    const ajouter = async (e) => {
        e.preventDefault(); // Empêche le formulaire de soumettre la page

        const name = e.target.name.value;
        const url = "http://127.0.0.1:8000/personne/new";

        const newItem = {
            prenom: name,
            nom: "a",
            is_coming: null, // Vous pouvez adapter ceci selon vos besoins
        };

        try {
            const response = await axios.post(url, newItem);
            const newPersonne = response.data;

            // Mettre à jour la liste des personnes dans le state React
            setIngredients([...ingredients, newPersonne]);

            // Effacer le champ de saisie après l'ajout
            e.target.name.value = "";
        } catch (error) {
            console.error("Erreur lors de l'ajout de la personne:", error);
        }
    };

    const enlever = async (ev, id) => {
        ev.preventDefault(); // Empêche le comportement par défaut du clic

        const deleteUrl = `http://127.0.0.1:8000/personne/${id}`;

        try {
            await axios.delete(deleteUrl);

            // Mettre à jour la liste des personnes dans le state React
            const updatedIngredients = ingredients.filter((el) => el.id !== id);
            setIngredients(updatedIngredients);
        } catch (error) {
            console.error(
                "Erreur lors de la suppression de la personne:",
                error
            );
        }
    };

    return (
        <>
            <section>
                <form method="post" onSubmit={ajouter}>
                    <input type="text" name="name" />
                    <button type="submit">add</button>
                </form>
            </section>
            <section className="table">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th colSpan="2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {ingredients.map((el) => (
                            <tr key={el.id}>
                                <td>{el.id}</td>
                                <td>{el.prenom}</td>
                                <td>
                                    <p onClick={(ev) => enlever(ev, el.id)}>
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            fill="currentColor"
                                            className="bi bi-trash-fill"
                                            viewBox="0 0 16 16"
                                        >
                                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                                        </svg>
                                    </p>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </section>
        </>
    );
}

export default App;
