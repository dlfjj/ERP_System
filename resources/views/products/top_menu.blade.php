<li><a href="/products/{{$product->id}}">General</a></li>
@if(has_role('products_prices'))
  <li><a href="/products/prices/{{$product->id}}">Prices</a></li>
@endif
<li><a href="/products/attributes/{{$product->id}}">Attributes</a></li>
<li><a href="/products/images/{{$product->id}}">Images</a></li>
<li><a href="/products/downloads/{{$product->id}}">Downloads</a></li>
<li><a href="/products/stock/{{$product->id}}">Stock</a></li>
<li><a href="/products/setup/{{$product->id}}">Setup</a></li>
@if(has_role('admin') && return_company_id() == 1)
<li><a href="/products/getSync/{{$product->id}}">Sync</a></li>
@endif
