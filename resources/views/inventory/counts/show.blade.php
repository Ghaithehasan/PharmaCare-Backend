<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الجرد</title>
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
                        <h5 class="mb-0">تفاصيل الجرد</h5>
                        <div>
                            <a href="#" class="btn btn-secondary">عودة</a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                إضافة دواء
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <strong>التاريخ:</strong> 2024-03-20
                            </div>
                            <div class="col-md-3">
                                <strong>الحالة:</strong> <span class="badge bg-warning">قيد التنفيذ</span>
                            </div>
                            <div class="col-md-3">
                                <strong>عدد الأدوية:</strong> 75
                            </div>
                            <div class="col-md-3">
                                <strong>ملاحظات:</strong> جرد دوري شهري
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الدواء</th>
                                        <th>الكمية الفعلية</th>
                                        <th>الكمية المسجلة</th>
                                        <th>الفرق</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>باراسيتامول 500</td>
                                        <td>100</td>
                                        <td>95</td>
                                        <td class="text-danger">-5</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="editItem(1)">تعديل</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>أموكسيسيلين 250</td>
                                        <td>50</td>
                                        <td>50</td>
                                        <td class="text-success">0</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="editItem(2)">تعديل</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-success" onclick="completeCount()">
                                إكمال الجرد
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal إضافة دواء -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة دواء للجرد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addItemForm">
                        <div class="mb-3">
                            <label class="form-label">الدواء</label>
                            <select class="form-select" name="medicine_id" required>
                                <option value="">اختر الدواء</option>
                                <option value="1">باراسيتامول 500</option>
                                <option value="2">أموكسيسيلين 250</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الكمية الفعلية</label>
                            <input type="number" class="form-control" name="actual_quantity" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="addItem()">إضافة</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal تعديل دواء -->
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل كمية الدواء</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editItemForm">
                        <input type="hidden" name="item_id">
                        <div class="mb-3">
                            <label class="form-label">الكمية الفعلية</label>
                            <input type="number" class="form-control" name="actual_quantity" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="updateItem()">حفظ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function addItem() {
        // محاكاة إضافة دواء
        alert('تم إضافة الدواء بنجاح');
        window.location.reload();
    }

    function editItem(id) {
        // محاكاة فتح نافذة التعديل
        $('#editItemModal').modal('show');
    }

    function updateItem() {
        // محاكاة تحديث الكمية
        alert('تم تحديث الكمية بنجاح');
        window.location.reload();
    }

    function completeCount() {
        if (confirm('هل أنت متأكد من إكمال الجرد؟')) {
            // محاكاة إكمال الجرد
            alert('تم إكمال الجرد بنجاح');
            window.location.href = '/inventory/counts';
        }
    }
    </script>
</body>
</html>
