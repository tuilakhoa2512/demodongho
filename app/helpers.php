	function getCompareSlots()
{
    $compare = session('compare', []);

    return [
        'sp1' => isset($compare['sp1']) ? App\Models\Product::find($compare['sp1']) : null,
        'sp2' => isset($compare['sp2']) ? App\Models\Product::find($compare['sp2']) : null,
    ];
}
