<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 *
 * @method \App\Model\Entity\Order[] paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $orders = $this->paginate($this->Orders);

        $this->set(compact('orders'));
        $this->set('_serialize', ['orders']);
    }

    /**
     * View method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => []
        ]);

        $this->set('order', $order);
        $this->set('_serialize', ['order']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $order = $this->Orders->newEntity();
        if ($this->request->is('post')) {
            $order = $this->Orders->patchEntity($order, $this->request->getData());
            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The order has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The order could not be saved. Please, try again.'));
        }
        $this->set(compact('order'));
        $this->set('_serialize', ['order']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $order = $this->Orders->patchEntity($order, $this->request->getData());
            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The order has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The order could not be saved. Please, try again.'));
        }
        $this->set(compact('order'));
        $this->set('_serialize', ['order']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $order = $this->Orders->get($id);
        if ($this->Orders->delete($order)) {
            $this->Flash->success(__('The order has been deleted.'));
        } else {
            $this->Flash->error(__('The order could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function import()
    {
        if ($this->request->is('post')) {
            $test=$this->request->data['csvfile'];
            var_dump($test);
            $up_file=$test['tmp_name'];
            var_dump($up_file);
            $filename='/var/www/caketest/orders.csv';
            if (is_uploaded_file($up_file)) {
                move_uploaded_file($up_file, $filename);
                $csvdata=file($filename, FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);
                foreach ($csvdata as $line) {
                    $record=explode(',', $line);
                    $data=array(
                        'id'=> $record[0],
                        'apple_order_num'=>$record[1],
                        'cmj_order_num'=>$record[2],
                        'status'=>$record[3],
                    );
                    $query=$this->Orders->query();
                    $query->insert(['id','apple_order_num','cmj_order_num','status'])
                          ->values(['id'=>$data['id'],'apple_order_num'=>$data['apple_order_num'],'cmj_order_num'=>$data['cmj_order_num'],'status'=>$data['status']])
                          ->execute();
                }
                var_dump($data);
                $this->Flash->success('uploaded');
                $this->redirect(array('action'=>'index'));
            }
        }
    }
}
