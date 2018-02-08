# jQuery DataTables plugin

##  Example of retrieving table rows

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