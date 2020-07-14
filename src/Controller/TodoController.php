<?php

namespace App\Controller;

use App\Model\TodoModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * 
 * @Route("/todo", name="todo_")
 */
class TodoController extends AbstractController
{
    /**
     * Liste des tâches
     *
     * @Route("s", name="list", methods={"GET"})
     */
    public function list()
    {
        $todos = TodoModel::findAll();

        return $this->render('todo/list.html.twig', [
            'todos' => $todos,
        ]);
    }

    /**
     * Affichage d'une tâche
     *
     * @Route("/{id}", name="show", requirements={"id" = "\d+"}, methods={"GET"})
     */
    public function show($id)
    {
        $todo = TodoModel::find($id);

        // if (!$todo)
        if ($todo === false) {
            throw $this->createNotFoundException('La tâche demandée n\'existe pas');
        }

        return $this->render('todo/single.html.twig', [
            'todo' => $todo
        ]);
    }

    /**
     * Changement de statut
     *
     * @Route(
     *      "/{id}/{status}",
     *      name="set_status",
     *      requirements={
     *          "id" = "\d+",
     *          "status" = "(un)?done"
     *      },
     *      methods={"POST"}
     * )
     */
    public function setStatus($id, $status)
    {

        if(TodoModel::setStatus($id, $status)) {
            $this->addFlash('success', 'La tâche a été marquée comme '. $status);
        } else {
            $this->addFlash('danger', 'La tâche n\'a pas pu être modifiée !');
        }

        return $this->redirectToRoute('todo_list');
    }

    /**
     * Ajout d'une tâche
     *
     * @Route("/add", name="add", methods={"POST"})
     *
     */
    public function add(Request $request)
    {

        $task = $request->request->get('task');

        TodoModel::add($task);

        $this->addFlash('success', 'La tâche «'.$task.'» a bien été ajoutée');

        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id" = "\d+"}, methods={"POST"})
     */
    public function delete($id)
    {
        if(TodoModel::delete($id)) {
            $this->addFlash('success', 'La tâche a bien été supprimée');
        } else {
            $this->addFlash('danger', 'Nous n\'avons pas pu supprimer la tâche');
        }

        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("s/reset", name="reset")
     */
    public function reset()
    {
        if($_ENV['APP_ENV'] == 'dev') {
            TodoModel::reset();
        }

        return $this->redirectToRoute('todo_list');
    }
}
