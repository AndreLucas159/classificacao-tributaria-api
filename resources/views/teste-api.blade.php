<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisador de Classificação Tributária</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-50">
    <div class="container px-4 py-8 mx-auto">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="mb-2 text-3xl font-bold text-gray-900">Analisador de Classificação Tributária</h1>
            <p class="text-gray-600">Analise a consistência e obtenha a classificação fiscal sugerida</p>
        </div>

        <!-- Form -->
        <div class="max-w-4xl p-6 mx-auto mb-8 bg-white shadow-md rounded-xl">
            <form id="analysis-form" class="space-y-6">
                <div>
                    <label for="descricao" class="block mb-2 text-sm font-medium text-gray-700">
                        Descrição do Produto <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        rows="4"
                        class="w-full px-3 py-2 transition duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Parafuso de aço para uso em madeira"
                        required
                    ></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label for="ncm_sh" class="block mb-2 text-sm font-medium text-gray-700">NCM/SH</label>
                        <input
                            type="text"
                            id="ncm_sh"
                            name="ncm_sh"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ex: 7318.15.00"
                        >
                    </div>

                    <div>
                        <label for="cest" class="block mb-2 text-sm font-medium text-gray-700">CEST</label>
                        <input
                            type="text"
                            id="cest"
                            name="cest"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ex: 10.068.00"
                        >
                    </div>

                    <div>
                        <label for="item" class="block mb-2 text-sm font-medium text-gray-700">Item</label>
                        <input
                            type="text"
                            id="item"
                            name="item"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ex: 68.0"
                        >
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        onclick="testarApis()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 transition duration-200 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    >
                        Testar APIs
                    </button>
                    <button
                        type="submit"
                        id="submit-button"
                        class="flex items-center px-6 py-2 text-sm font-medium text-white transition duration-200 bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <span id="button-text">Analisar Produto</span>
                        <svg id="loading-spinner" class="hidden w-4 h-4 ml-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Results -->
        <div id="response-container" class="hidden max-w-4xl mx-auto">
            <div class="p-6 bg-white shadow-md rounded-xl">
                <h2 class="mb-4 text-xl font-semibold text-gray-900">Resultado da Análise</h2>

                <!-- Loading -->
                <div id="loading" class="hidden py-8 text-center">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-600">Analisando... Isso pode levar alguns segundos.</span>
                    </div>
                </div>

                <!-- Error -->
                <div id="error" class="hidden p-4 mb-4 border border-red-200 rounded-lg bg-red-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <strong class="font-medium text-red-800">Erro!</strong>
                    </div>
                    <p id="error-message" class="mt-1 text-red-700"></p>
                </div>

                <!-- Success -->
                <div id="success" class="hidden">
                    <div class="p-3 mb-4 border border-green-200 rounded-lg bg-green-50">
                        <div class="flex items-center text-green-800">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span id="api-source" class="font-medium"></span>
                        </div>
                    </div>
                    <pre id="response-output" class="p-4 overflow-x-auto text-sm text-green-400 bg-gray-900 rounded-lg"></pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function testarApis() {
            try {
                const response = await fetch("{{ route('api.testar') }}");
                const result = await response.json();
                alert('Gemini: ' + (result.gemini.success ? '✅ OK' : '❌ Falhou') +
                      '\nDeepSeek: ' + (result.deepseek.success ? '✅ OK' : '❌ Falhou'));
            } catch (error) {
                alert('Erro ao testar APIs: ' + error.message);
            }
        }

        document.getElementById('analysis-form').addEventListener('submit', async function (event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());

            const responseContainer = document.getElementById('response-container');
            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            const success = document.getElementById('success');
            const responseOutput = document.getElementById('response-output');
            const errorMessage = document.getElementById('error-message');
            const apiSource = document.getElementById('api-source');
            const submitButton = document.getElementById('submit-button');
            const buttonText = document.getElementById('button-text');
            const loadingSpinner = document.getElementById('loading-spinner');

            // Reset states
            responseContainer.classList.remove('hidden');
            loading.classList.remove('hidden');
            error.classList.add('hidden');
            success.classList.add('hidden');
            submitButton.disabled = true;
            buttonText.textContent = 'Analisando...';
            loadingSpinner.classList.remove('hidden');

            try {
                const response = await fetch("{{ url('/api/analisar-produto') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'Erro na requisição');
                }

                // Show success
                loading.classList.add('hidden');
                success.classList.remove('hidden');
                responseOutput.textContent = JSON.stringify(result, null, 2);
                apiSource.textContent = `Fonte: ${result.source || 'gemini'}`;

            } catch (err) {
                loading.classList.add('hidden');
                error.classList.remove('hidden');
                errorMessage.textContent = err.message;
            } finally {
                submitButton.disabled = false;
                buttonText.textContent = 'Analisar Produto';
                loadingSpinner.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
