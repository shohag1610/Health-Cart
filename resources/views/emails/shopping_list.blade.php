<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping List</title>
</head>

<body>
    <h1>{{ $user->name }}'s Shopping List</h1>
    <h2>Items to Buy</h2>
    <ul>
        @foreach ($uncheckedItems as $item)
            <li>{{ $item->item_name }} - £{{ $item->item_price }}</li>
        @endforeach
    </ul>

    <h2>Items Already Bought</h2>
    <ul>
        @foreach ($checkedItems as $item)
            <li>{{ $item->item_name }} - £{{ $item->item_price }}</li>
        @endforeach
    </ul>
</body>

</html>
