<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Matiere;
use App\Entity\Note;

use App\Form\NoteType;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, TranslatorInterface $trans)
    {
        $em = $this->getDoctrine()->getManager();

        $note = new Note();
        $note->setDateadded(new \DateTime('now'));

        $form = $this->createForm(NoteType::class, $note);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em->persist($note);
            $em->flush();

            $this->addFlash(
                'success',
                $trans->trans('note.added')
            );
        }

        $notes = $em->getRepository(Note::class)->findAll();
        $matieres = $em->getRepository(Matiere::class);

        $count = count($notes);

        $resultat = 0;
        $allCoef = 0;

        for ($i=0; $i < $count ; $i++) {
            $calcNote = $notes{$i}->getNote();

            $calcCoef = $notes{$i}->getMatiere()->getCoefficient();

            $resultat = $resultat + ($calcNote * $calcCoef);
            $allCoef = $allCoef + $calcCoef;
        }

        if ($count > 0) {

          $moyenne = $resultat/$allCoef;
        }


        return $this->render('default/index.html.twig', [
            'notes' => $notes,
            'moyenne' => $moyenne,
            'add_note' => $form->createView()
        ]);
    }

}
