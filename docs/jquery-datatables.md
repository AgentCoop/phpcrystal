# jQuery DataTables plugin

##  Example of retrieving table rows

```javascript
        $(document).ready(function() {
            var $dt = $('#order-table').dataTable( {
                "order": [[ 2, "desc" ]],
                "pageLength": 10,
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api_fetch_order_records') }}",
                "deferLoading": 100,
                "columnDefs": [
                    { className: "nowrap", "targets": [ 2,3,4,5 ] },
                ],
                "columns": [
                    { "data": "order_num" },
                    { "data": "order_id" },
                    { "data": "created_at" },
                    { "data": "closed_at" },
                    { "data": "processed_at" },
                    { "data": "cancelled_at" },
                    { "data": "total_price" },
                    { "data": "cust_name" },
                    { "data": "cust_email" },
                    { "data": "status" }
                ]
            });

            $dt.fnDraw();
        });
```

```php
    /**
     *
     */
    public function fetchOrderRecords(Request $request)
    {
        try {
            $offset = $request->input('start');
            $pageLength = $request->input('length');
            $pageNum = ($offset / $pageLength) + 1;

            $orderCb = $filterCb = null;
            
            $orderDef = @$request->input('order', null)[0];
            $searchDef = $request->input('search', null);
            
            // Order by a column if needed
            if ($orderDef) {
                $orderCallback = function($query) use ($orderDef) {
                    switch (intval($orderDef['column'])) {
                        case 0:
                            $query->orderBy('order_number', $orderDef['dir']);
                            break;
                        case 2:
                            $query->orderBy(Order::CREATED_AT, $orderDef['dir']);
                            break;
                    }
                };
            }
            
            // Filter out some rows
            if ( ! empty($searchDef['value'])) {
                $needle = $searchDef['value'];
                $filterCb = function($item, $key) use($needle) {
                    return stripos($item['cust_name'], $needle) !== false;
                };
            }
            
            $pagerData = Order::getPaged($offset, $pageLength, $orderCb);
            
            $tableRecords = BackofficeView\Order\ListView::create($pagerData['items'])
                ->getDataTablesRows(Order::getTotalCount(), $filterCb);
                
            return $this->responseJson($tableRecords);
        } catch (Loggable $e) {
            return $this->failed($e->getCode(), $e->getMessage(), $e->getDataBag());
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
```