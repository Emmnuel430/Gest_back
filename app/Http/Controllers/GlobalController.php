<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Etudiant;
use App\Models\Moniteur;
use App\Models\Progression;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class GlobalController extends Controller
{
    // Évolution du nombre d'inscriptions dans le temps
    // Cette méthode retourne le nombre d'inscriptions d'étudiants par jour.
    public function evolutionInscriptions()
    {
        $data = Etudiant::selectRaw('DATE(created_at) as date, COUNT(*) as count') // Sélectionne la date et le nombre d'inscriptions
            ->groupBy('date') // Regroupe les résultats par date
            ->orderBy('date') // Trie les résultats par date
            ->get();

        return response()->json($data); // Retourne les données au format JSON
    }

    // Classement des étudiants par étape de progression
    // Cette méthode retourne le nombre d'étudiants pour chaque étape de progression.
    public function etudiantsParEtape()
    {
        $data = Progression::select('etape', DB::raw('COUNT(*) as count')) // Sélectionne l'étape et le nombre d'étudiants
            ->groupBy('etape') // Regroupe les résultats par étape
            ->orderBy('etape') // Trie les résultats par étape
            ->get();

        return response()->json($data); // Retourne les données au format JSON
    }

    // Répartition par catégorie de permis
    // Cette méthode retourne le nombre d'étudiants par catégorie de permis.
    public function repartitionParCategorie()
    {
        $data = Etudiant::select('categorie', DB::raw('COUNT(*) as count')) // Sélectionne la catégorie et le nombre d'étudiants
            ->groupBy('categorie') // Regroupe les résultats par catégorie
            ->get();

        return response()->json($data); // Retourne les données au format JSON
    }

    // Répartition par moniteur
    // Cette méthode retourne le nombre d'étudiants assignés à chaque moniteur.
    public function repartitionParMoniteur()
    {
        $data = Etudiant::select('idMoniteur', DB::raw('COUNT(*) as count')) // Sélectionne l'ID du moniteur et le nombre d'étudiants
            ->with('moniteur') // Charge les informations du moniteur associé
            ->groupBy('idMoniteur') // Regroupe les résultats par moniteur
            ->get();

        return response()->json($data); // Retourne les données au format JSON
    }

    // Répartition par réduction
    // Cette méthode retourne le nombre d'étudiants bénéficiant ou non d'une réduction.
    public function repartitionParReduction()
    {
        $data = Etudiant::select('reduction', DB::raw('COUNT(*) as count')) // Sélectionne l'état de réduction et le nombre d'étudiants
            ->groupBy('reduction') // Regroupe les résultats par état de réduction
            ->get();

        return response()->json($data); // Retourne les données au format JSON
    }

    // Totaux des entités principales
    // Cette méthode retourne le nombre total d'étudiants, de moniteurs et d'utilisateurs.
    public function totaux()
    {
        $totalEtudiants = Etudiant::count(); // Compte le nombre total d'étudiants
        $totalMoniteurs = Moniteur::count(); // Compte le nombre total de moniteurs
        $totalUsers = User::count(); // Compte le nombre total d'utilisateurs
        // -----
        $totalMontantPaye = Etudiant::sum('montant_paye'); // Somme de tous les montants payés par les étudiants
        $etudiantsAuCode = Etudiant::whereHas('progression', function ($query) {
            $query->where('etape', 'cours_de_code') // Étape "code"
                ->orWhere('etape', 'examen_de_code'); // Étape "examen_code"
        })->get();
        $etudiantsALaConduite = Etudiant::whereHas('progression', function ($query) {
            $query->where('etape', 'cours_de_conduite') // Étape "conduite"
                ->orWhere('etape', 'examen_de_conduite'); // Étape "examen_conduite"
        })->get();

        return response()->json([
            'totalEtudiants' => $totalEtudiants, // Retourne le total des étudiants
            'totalMoniteurs' => $totalMoniteurs, // Retourne le total des moniteurs
            'totalUsers' => $totalUsers, // Retourne le total des utilisateurs
            'totalMontantPaye' => $totalMontantPaye, // Retourne la somme des montants payés
            'etudiantsAuCode' => $etudiantsAuCode->count(), // Retourne le nombre d'étudiants au code
            'etudiantsALaConduite' => $etudiantsALaConduite->count(), // Retourne le nombre d'étudiants à la conduite
        ]);
    }


}
