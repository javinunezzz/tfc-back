<?php

namespace Database\Factories;

use App\Models\Asignatura;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apunte>
 */
class ApunteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $usuario = User::where('id', '!=', 1)->inRandomOrder()->first();
        $categoria = Categoria::inRandomOrder()->first();
        $asignatura = Asignatura::inRandomOrder()->first();

        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();

        // Listas de títulos y descripciones emparejadas
        $titulos = [
            'Matrices y Determinantes',
            'La Guerra Civil Española',
            'POO y Herencia en Java',
            'Leyes del Movimiento de Newton',
            'Genética y Herencia Mendeliana',
            'Derivadas e Integrales',
            'Enlaces Químicos y Reacciones',
            'Conceptos Básicos de Microeconomía',
            'Realismo y Naturalismo',
            'Climas y Biomas Terrestres',
            'Conceptos Básicos del Derecho Civil',
            'Teorías del Aprendizaje',
            'Combinatoria y Probabilidad',
            'Impresionismo y Vanguardias',
            'Estructuras de Datos en Python',
            'Kant y la Crítica de la Razón Pura',
            'Estructuras y Dinámicas Sociales',
            'Redes y Protocolo TCP/IP',
            'Distribuciones de Probabilidad',
            'Civilizaciones Mayas y Aztecas',
            'Circuitos Digitales y Analógicos',
            'Ecuaciones Diferenciales',
            'Cinemática, Movimiento y Velocidad',
            'La Tabla Periódica y sus Elementos',
            'Ecosistemas y Cadenas Tróficas',
            'Don Quijote y el Siglo de Oro',
            'HTML y CSS',
            'Macroeconomía e Indicadores',
            'Memoria y Procesos Cognitivos',
            'Principios Básicos del Derecho Penal',
            'Mapas y Sistemas de Coordenadas',
            'Imperio y República Romana',
            'Test de Hipótesis y Significancia',
            'Series y Sucesiones Numéricas',
            'Análisis y Funciones Matemáticas',
            'Estructuras y Punteros en C',
            'El Feudalismo',
            'Sistemas y Bases de Datos',
            'Patrones de Diseño en Software',
            'Procesos Productivos en la Industria',
            'Nietzsche y la Voluntad de Poder',
            'Sensores y Microcontroladores',
            'Equilibrios Químicos y Ácidos-Bases',
            'Sistemas Montañosos y Ríos',
            'Células y Organización Tisular',
            'Democracia y Filosofía en Grecia',
            'Modernismo y Generación del 98',
            'DOM y Eventos en JavaScript',
            'Termodinámica y Energía',
            'Regresión y Correlación',
            'Matrices y Espacios Vectoriales',
            'Desarrollo y Conducta Humana',
            'Propiedad Intelectual y Patentes',
            'Fallas y Placas Tectónicas',
            'Finanzas y Mercados Bursátiles',
            'Guerras Mundiales',
            'Base de Datos con MySQL',
            'Desigualdad y Movimientos Sociales',
            'Algoritmos y Complejidad',
            'Sostenibilidad y Materiales en Arquitectura',
            'Química Orgánica e Hidrocarburos',
            'Puentes y Estructuras en Ingeniería Civil',
            'Funciones y Límites',
            'Ciencias del Medio Ambiente',
            'Movimientos Sociales del Siglo XX',
            'Bécquer, Poesía y Romanticismo',
            'Mecánica Ondulatoria y Sonido',
            'Desarrollo de Apps en iOS con Swift',
            'Contratos y Obligaciones Legales',
            'Psicoanálisis y Freud',
            'Meteorología y Cambio Climático',
            'Muestreo y Teoría de Estimación',
            'Neolítico y Edad de los Metales',
            'Desarrollo en Android con Kotlin',
            'Economía Internacional y Comercio',
            'Optimización y Programación Lineal',
            'Existencialismo y Sartre',
            'Procesadores y Memoria en Computadores',
            'Renacimiento, Arte y Ciencia',
            'Paradigma Orientado a Objetos en Ruby',
            'Electromagnetismo y Campos',
            'Reacciones Redox y Electroquímica',
            'Ciberseguridad y Criptografía',
            'Justicia y Sistemas Judiciales',
            'Mineralogía y Rocas',
            'Descubrimientos Geográficos',
            'Terapias Conductuales y Cognitivas'
        ];

        $descripciones = [
            'Estudio de matrices y determinantes en álgebra lineal',
            'Análisis de la Guerra Civil Española y sus consecuencias',
            'Programación orientada a objetos y herencia en Java',
            'Leyes fundamentales del movimiento de Newton en física',
            'Estudio de la genética y la herencia mendeliana en biología',
            'Conceptos fundamentales de derivadas e integrales en cálculo',
            'Reacciones químicas y enlaces en compuestos químicos básicos',
            'Introducción a la microeconomía y sus conceptos fundamentales',
            'Análisis literario del realismo y naturalismo en la literatura',
            'Estudio de climas y biomas terrestres en geografía',
            'Fundamentos del derecho civil y sus aplicaciones prácticas',
            'Teorías del aprendizaje y su impacto en la educación moderna',
            'Combinatoria y probabilidad en estadística aplicada',
            'Movimientos artísticos del impresionismo y las vanguardias',
            'Estudio de estructuras de datos en Python para programación',
            'Filosofía de Kant y su crítica de la razón pura',
            'Estudio de las estructuras y dinámicas sociales humanas',
            'Redes de computadoras y protocolo TCP/IP en comunicaciones',
            'Distribuciones de probabilidad en estadística y su aplicación',
            'Civilizaciones mayas y aztecas en la historia antigua',
            'Análisis de circuitos digitales y analógicos en electrónica',
            'Ecuaciones diferenciales y su resolución en matemáticas aplicadas',
            'Estudio de la cinemática, movimiento y velocidad en física',
            'Estudio de la tabla periódica y sus elementos químicos',
            'Ecosistemas y cadenas tróficas en biología y ecología',
            'Análisis literario de Don Quijote y el Siglo de Oro',
            'Fundamentos de HTML y CSS en el desarrollo web moderno',
            'Teoría macroeconómica y sus principales indicadores económicos',
            'Estudio de la memoria y procesos cognitivos en psicología',
            'Principios fundamentales del derecho penal y sus implicaciones',
            'Geografía de mapas y sistemas de coordenadas terrestres',
            'Historia del imperio y la república romana en la antigüedad',
            'Métodos estadísticos como el test de hipótesis y significancia',
            'Estudio de series y sucesiones numéricas en matemáticas',
            'Análisis de funciones matemáticas y sus aplicaciones prácticas',
            'Estructuras y punteros en el lenguaje de programación C',
            'Estudio histórico del feudalismo en la Edad Media',
            'Bases de datos y sistemas de gestión de información',
            'Patrones de diseño en programación y desarrollo de software',
            'Procesos productivos en la industria y su optimización',
            'Filosofía de Nietzsche y su concepto de la voluntad de poder',
            'Estudio de sensores y microcontroladores en ingeniería electrónica',
            'Equilibrios químicos y ácidos-bases en química general',
            'Estudio de sistemas montañosos y ríos en geografía',
            'Análisis de células y su organización en biología celular',
            'Filosofía de la democracia y la Grecia clásica',
            'Modernismo literario y la Generación del 98 en España',
            'Manipulación del DOM y eventos en JavaScript para web',
            'Teoría termodinámica y sus principios básicos en física',
            'Métodos de regresión y correlación en estadísticas avanzadas',
            'Matrices y espacios vectoriales en álgebra lineal',
            'Desarrollo y conducta humana en psicología evolutiva',
            'Propiedad intelectual y su gestión en el mundo moderno',
            'Estudio de fallas y placas tectónicas en geología',
            'Mercados bursátiles y análisis financiero en economía',
            'Estudio de las guerras mundiales en historia contemporánea',
            'Base de datos con MySQL y su gestión eficiente',
            'Estudio de desigualdad y movimientos sociales en la historia',
            'Algoritmos y complejidad computacional en ciencias de la computación',
            'Sostenibilidad y materiales en arquitectura moderna',
            'Química orgánica e hidrocarburos en la industria química',
            'Estudio de puentes y estructuras en ingeniería civil',
            'Cálculo de funciones y límites en matemáticas aplicadas',
            'Ciencias del medio ambiente y su preservación ecológica',
            'Movimientos sociales del siglo XX y su impacto global',
            'Estudio literario de Bécquer, poesía y romanticismo',
            'Mecánica ondulatoria y sonido en física',
            'Desarrollo de apps en iOS con Swift',
            'Contratos y obligaciones legales en derecho civil',
            'Psicoanálisis y teorías de Freud en psicología',
            'Meteorología y cambio climático en ciencias atmosféricas',
            'Teoría de muestreo y estimación en estadísticas',
            'Estudio del Neolítico y la Edad de los Metales',
            'Desarrollo en Android con Kotlin y sus aplicaciones',
            'Economía internacional y comercio global en estudios económicos',
            'Optimización y programación lineal en matemáticas aplicadas',
            'Existencialismo y las obras de Sartre en filosofía',
            'Procesadores y memoria en arquitecturas de computadores',
            'Renacimiento, arte y ciencia en la historia cultural',
            'Paradigma orientado a objetos en Ruby para programación',
            'Estudio del electromagnetismo y campos eléctricos y magnéticos',
            'Reacciones redox y electroquímica en química general',
            'Ciberseguridad y criptografía en tecnología informática',
            'Justicia y sistemas judiciales en derecho',
            'Mineralogía y estudio de rocas en geología',
            'Descubrimientos geográficos y su impacto en la historia',
            'Terapias conductuales y cognitivas en psicología clínica'
        ];

        // Emparejando título con descripción
        $index = array_rand($titulos);
        $titulo = $titulos[$index];
        $descripcion = $descripciones[$index];

        $fileName = $uuid . '.' . $titulo . '.pdf';
        $destino = env('UPLOAD_PDF', 'pdf');

        // Check if source file exists
        $sourcePath = public_path('pdf/ejemplo.pdf');
        if (!file_exists($sourcePath)) {
            throw new \Exception('Source PDF file not found in public/pdf/ejemplo.pdf');
        }

        // Ensure destination directory exists
        if (!Storage::exists($destino)) {
            Storage::makeDirectory($destino);
        }

        // Copy the file and verify
        Storage::put($destino . '/' . $fileName, file_get_contents($sourcePath));

        if (!Storage::exists($destino . '/' . $fileName)) {
            throw new \Exception('Failed to copy PDF file to destination');
        }

        return [
            'user_id' => 1, //ADMIN pero esto solo son pruebas
            'categoria_id' => $categoria->id,
            'asignatura_id' => $asignatura->id,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'pdf' => $fileName,
        ];
    }
}
