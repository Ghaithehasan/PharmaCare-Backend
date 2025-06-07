<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الجرد</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">قائمة الجرد</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#startCountModal">
                            بدء جرد جديد
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>التاريخ</th>
                                        <th>الحالة</th>
                                        <th>عدد الأدوية</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>2024-03-20</td>
                                        <td><span class="badge bg-success">مكتمل</span></td>
                                        <td>150</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info">عرض</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>2024-03-19</td>
                                        <td><span class="badge bg-warning">قيد التنفيذ</span></td>
                                        <td>75</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info">عرض</a>
                                            <a href="#" class="btn btn-sm btn-success">إكمال</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal بدء جرد جديد -->
    <div class="modal fade" id="startCountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">بدء جرد جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="startCountForm">
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="startNewCount()">بدء الجرد</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function startNewCount() {
        // محاكاة بدء جرد جديد
        alert('تم بدء جرد جديد بنجاح');
        window.location.reload();
    }
    </script>
</body>
</html>