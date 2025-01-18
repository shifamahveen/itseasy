<?php

namespace App\Models\core;

use GuzzleHttp\Psr7\Query;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\countOf;
use Illuminate\Database\QueryException;

class Update extends Model
{
    use HasFactory;
    public function getRecords()
    {
        $client_slug = client('slug');
        $search = request()->get('search');
        $filter = request()->get('filter');
        $role = user('role');
        if ($search) {
            $column = request()->get('column');
            if ($role == 'superadmin' || $role == 'rootadmin')
                return Update::where($column, 'Like', "%" . $search . "%")->orderBy('id', 'desc')->paginate(10);
            else
                return Update::where($column, 'Like', "%" . $search . "%")->where('client_slug', $client_slug)->orderBy('id', 'desc')->paginate(10);
        } else if ($filter) {
            if ($filter == 'active')
                $status = 1;
            else if ($filter == 'inactive')
                $status = 0;
            if ($role == 'superadmin' || $role == 'rootadmin')

                return Update::where('status', $status)->orderBy('id', 'desc')->paginate(10);
            else
                return Update::where('status', $status)->where('client_slug', $client_slug)->paginate(10);
        } else {
            if ($role == 'superadmin' || $role == 'rootadmin')

                return Update::orderBy('id', 'desc')->paginate(10);
            else
                return Update::where('client_slug', $client_slug)->orderBy('id', 'desc')->paginate(10);
        }
    }
    public function getCount()
    {
        $role = user('role');
        $client_slug = request()->get('client_slug');
        $count = ['active' => 0, 'inactive' => 0, 'all' => 0];
        if ($role == 'superadmin' || $role == 'rootadmin')

            $data = Update::select('status')->get()->groupBy('status');
        else
            $data = Update::select('status')->where('client_slug', $client_slug)->get()->groupBy('status');
        if (count($data)) {
            if (isset($data[1]))
                $count['active'] = count($data[1]);
            if (isset($data[0]))
                $count['inactive'] = count($data[0]);
            $count['all'] = $count['active'] + $count['inactive'];
        }
        return $count;
    }

    public function storeData()
    {
        try {
            $request = request();
            $model = new Self();
            $model->title = $request->get('title');
            $model->description = $request->get('description');
            $model->status = $request->get('status');
            $model->client_slug = $request->get('client_slug');
            $model->save();

            return "one record of Update is created";
        } catch (QueryException $exception) {
            // return error message
            return "Error: " . implode(',', $exception->errorInfo);
        }
    }

    public function updateData($id)
    {

        try {
            $request = request();
            $model = Self::where('id', $id)->first();
            $model->title = $request->get('title');
            $model->description = $request->get('description');
            $model->status = $request->get('status');
            $model->client_slug = $request->get('client_slug');
            $model->save();

            return "one record of Update is Updated";
        } catch (QueryException $exception) {
            // return error message
            return "Error: " . implode(',', $exception->errorInfo);
        }
    }
}
