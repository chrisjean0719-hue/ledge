# Ledge

An HTTP API for a timber raised bed. It takes the outer length and width, stacks courses of sleepers, can add a liner, can add a drainage layer, sizes the inner opening, and counts the timber and the soil.

It is for someone setting out a kitchen bed at home. The sample bed is made up. The service does not store names, addresses, or customer orders.

Written in PHP 8.3 or newer, with Laravel. Tests use PHPUnit.

## Run

```bash
composer install
php artisan test
php artisan serve --host=127.0.0.1 --port=8027
```

The server is at http://127.0.0.1:8027.

```bash
curl http://127.0.0.1:8027/api/sample
curl -X POST http://127.0.0.1:8027/api/beds -H "Content-Type: application/json" -d "{\"name\":\"Sample kitchen ledge\",\"lengthMm\":2400,\"widthMm\":1200,\"thicknessMm\":100,\"courseHeightMm\":125,\"courses\":2}"
```

A bed is a name, an outer length, and an outer width. Leave the sleeper thickness, the course height, and the course count out to keep 100 mm thick sleepers, 125 mm courses, and 2 courses. Each course is a ring of timber measured on the outer face, so one course is twice the length plus twice the width. The soil fills the inner opening to the full stacked height. The sample kitchen ledge is 2400 by 1200 mm with 2 courses: an inner opening of 2200 by 1000 mm, 250 mm of soil height, 14400 mm of timber, and 550 L of soil. Leave `liner` out, or set it to false, for no liner. Set `liner` to true to cover the floor and the four inside faces. The sample liner is 3800000 mm2. The timber and the soil stay the same. Leave `drainageMm` out for no drainage layer. A drainage layer is from 40 to 150 mm of gravel under the soil. The soil height shrinks by that depth, and the soil must stay at least 100 mm. A 50 mm drainage layer on the sample leaves 200 mm of soil, 440 L of soil, and 110 L of gravel. The liner still covers the full bed height.

## Layout

```
app/Ledge/      sizes, the sleeper courses, checks
routes/         the HTTP API
examples/       a fictional bed
tests/          unit tests
```

## Next

Three updates remain. After those, about 38 hours of later work stay in [ROADMAP.md](ROADMAP.md).

## License

MIT
