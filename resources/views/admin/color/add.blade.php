@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <h1>Add New Color</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <form Action="" method="post">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Color Name <span style="color:red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('name')}}" name="name" required
                                        placeholder="Color Name">
                                </div>
                                <div class="form-group">
                                    <label>Color Name <span style="color:red">*</span></label>
                                    <input type="color" class="form-control" value="{{ old('code')}}" name="code" required
                                        placeholder="Color Name">
                                </div>
                                <div class="form-group">
                                    <label>Status <span style="color:red">*</span></label>
                                    <select class="form-control" name="status" required>
                                        <option {{ (old('status') == 0) ? 'selected' : ''}} value="0">Active</option>
                                        <option {{ (old('status') == 1) ? 'selected' : ''}} value="1">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
@endsection
