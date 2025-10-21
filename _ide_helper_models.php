<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $secao_id
 * @property string|null $item
 * @property string|null $cest
 * @property string $ncm_sh
 * @property string $descricao
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $ato_legal
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RegraTributaria> $regrasTributarias
 * @property-read int|null $regras_tributarias_count
 * @property-read \App\Models\Secao $secao
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto whereAtoLegal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto whereCest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto whereDescricao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto whereNcmSh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto whereSecaoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Produto whereUpdatedAt($value)
 */
	class Produto extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $produto_id
 * @property string|null $ato_legal
 * @property string|null $mva_original
 * @property string|null $multiplicador_original
 * @property string|null $mva_ajustada
 * @property string|null $multiplicador_ajustado
 * @property string $aliquota_interna
 * @property string $aliquota_interestadual
 * @property string|null $descricao_extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Produto $produto
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereAliquotaInterestadual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereAliquotaInterna($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereAtoLegal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereDescricaoExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereMultiplicadorAjustado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereMultiplicadorOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereMvaAjustada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereMvaOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereProdutoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegraTributaria whereUpdatedAt($value)
 */
	class RegraTributaria extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nome
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Produto> $produtos
 * @property-read int|null $produtos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secao newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secao newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secao query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secao whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secao whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secao whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secao whereUpdatedAt($value)
 */
	class Secao extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

