<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WhatsappInstanceResource\{Pages};
use App\Models\WhatsappInstance;
use App\Services\Evolution\Instance\{ConnectEvolutionInstanceService, DeleteEvolutionInstanceService};
use Filament\Facades\Filament;
use Filament\Forms\Components\{Section, TextInput, ToggleButtons};
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\{Action, ActionGroup, DeleteAction, EditAction, ViewAction};
use Filament\Tables\Table;
use Leandrocfe\FilamentPtbrFormFields\PhoneNumber;

class WhatsappInstanceResource extends Resource
{
    protected static ?string $model = WhatsappInstance::class;

    protected static ?string $navigationIcon = 'fab-whatsapp';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Instâncias WhatsApp';

    protected static ?string $modelLabel = 'Instâncias WhatsApp';

    protected static ?string $modelLabelPlural = "Instâncias WhatsApp";

    protected static ?int $navigationSort = 3;

    protected static bool $isScopedToTenant = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados da Instância')
                    ->schema([

                        TextInput::make('name')
                            ->label('Nome da Instância')
                            ->unique(WhatsappInstance::class, 'name', ignoreRecord: true)
                            ->default(fn () => Filament::getTenant()?->slug ?? '')
                            ->required()
                            ->prefixIcon('fas-id-card')
                            ->validationMessages([
                                'unique' => 'Nome da instância já cadastrada.',
                            ])
                            ->maxLength(20),

                        PhoneNumber::make('number')
                            ->label('Número WhatsApp')
                            ->unique(WhatsappInstance::class, 'number', ignoreRecord: true)
                            ->mask('+55 (99) 99999-9999')
                            ->placeholder('+55 (99) 99999-9999')
                            ->required()
                            ->prefixIcon('fas-phone')
                            ->validationMessages([
                                'unique' => 'Número já cadastrado.',
                            ]),

                    ])->columns(2),

                Section::make('Dados da Instância')
                    ->schema([
                        ToggleButtons::make('groups_ignore')
                        ->label('Ignorar Grupos')
                        ->inline()
                        ->boolean()
                        ->required(),

                        ToggleButtons::make('always_online')
                        ->label('Status Sempre Online')
                        ->inline()
                        ->boolean()
                        ->required(),

                        ToggleButtons::make('read_messages')
                        ->label('Marcar Mensagens como Lidas')
                        ->inline()
                        ->boolean()
                        ->required(),

                        ToggleButtons::make('read_status')
                        ->label('Marcar Status como Lido')
                        ->inline()
                        ->boolean()
                        ->required(),

                        ToggleButtons::make('sync_full_history')
                        ->label('Sincronizar Histórico')
                        ->inline()
                        ->boolean()
                        ->required(),

                        ToggleButtons::make('reject_call')
                        ->label('Rejeitar Chamadas')
                        ->inline()
                        ->boolean()
                        ->live()
                        ->reactive()
                        ->required(),

                        TextInput::make('msg_call')
                        ->label('Mensagem para Chamadas Rejeitadas')
                        ->required()
                        ->hidden(fn ($get) => $get('reject_call') == false)
                        ->maxLength(255),

                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->alignCenter()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome da Instância')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->searchable(),
                Tables\Columns\TextColumn::make('instance_id')
                    ->label('ID da Instância')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('showQr')
                        ->label('QR Code')
                        ->icon('heroicon-o-qr-code')
                        ->color('success')
                        ->modalHeading('Qr Code WhatsApp')
                        ->modalSubmitAction(false)
                        ->modalCancelAction(
                            \Filament\Actions\Action::make('close')
                                ->label('FECHAR')
                                ->color('danger') // Cores: primary, secondary, success, danger, warning, gray
                                ->extraAttributes(['class' => 'w-full']) // Largura total
                                ->close()
                        )
                        ->modalWidth('md') // ou sm, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl
                        ->modalContent(fn ($record) => view('evolution.qr-code-modal', [
                            'qrCode' => str_replace('\/', '/', $record->getRawOriginal('qr_code')),
                        ])),

                ActionGroup::make([
                    Action::make('RestartInstance')
                    ->label('Reconectar Instância')
                    ->icon('fas-sign-in-alt')
                    ->color('info')
                    ->action(function ($record) {
                        $service  = new ConnectEvolutionInstanceService();
                        $response = $service->connectInstance($record->name);

                        if (isset($response['error'])) {
                            Notification::make()
                                ->title('Erro ao reconectar')
                                ->danger()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Instância reconectada')
                                ->success()
                                ->send();
                        }
                    }),

                    ViewAction::make()
                        ->color('primary'),
                    EditAction::make()
                        ->color('secondary'),
                    DeleteAction::make()
                        ->action(function ($record) {

                            $service  = new DeleteEvolutionInstanceService();
                            $response = $service->deleteInstance($record->name);

                            // Deleta o registro local após sucesso na API
                            $record->delete();

                        }),
                ])
                ->icon('fas-sliders')
                ->color('warning'),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWhatsappInstances::route('/'),
            'create' => Pages\CreateWhatsappInstance::route('/create'),
            'view'   => Pages\ViewWhatsappInstance::route('/{record}'),
            'edit'   => Pages\EditWhatsappInstance::route('/{record}/edit'),
        ];
    }
}
