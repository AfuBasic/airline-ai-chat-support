<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentsResource\Pages;
use App\Filament\Resources\AgentsResource\RelationManagers;
use App\Models\User;
use Dom\Text;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class AgentsResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        $password = Str::random(12);
        return $form
            ->schema([
                TextInput::make('name')
                ->required()
                ->label('Agent\'s name'),
                
                TextInput::make('email')->required()->email(),

                TextInput::make('password')
                ->helperText('Please copy the password and save it securely.')
                ->label( 'Password')
                ->readOnly()
                ->default($password)
                ->hiddenOn('edit')
                ->columnSpanFull()
                ->suffixAction(
                    Action::make('copy')
                        ->icon('heroicon-s-clipboard-document-check')
                        ->action(function ($livewire, $state) {
                            $livewire->js(
                                'window.navigator.clipboard.writeText("'.$state.'");
                                $tooltip("'.__('Copied to clipboard').'", { timeout: 2500 });'
                            );
                        })
                ),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Agent\'s Name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->modifyQueryUsing(function (Builder $query) {
               $query->where('user_type', 'agent');
            })
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Reset Password')
                    ->icon('heroicon-o-key')
                    ->requiresConfirmation(function (Tables\Actions\Action $action, $record){
                        $action->modalDescription('Are you sure you want to reset the password for' . $record->name .'?');
                        $action->modalHeading('Reset Password');
                        return $action;
                    })
                    ->action(function(User $record){
                        $user = User::find($record->id);
                        $newPassword = Str::random(12);
                        $user->password = $newPassword;
                        $user->first_time = true;
                        $user->save();

                        $notification = Notification::make()
                            ->title('Password Reset Successfully')
                            ->body('The password for ' . $record->name . ' has been reset to: ' . $newPassword)
                            ->success()
                            ->persistent()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAgents::route('/'),
            // 'create' => Pages\CreateAgents::route('/create'),
            // 'edit' => Pages\EditAgents::route('/{record}/edit'),
        ];
    }
}
