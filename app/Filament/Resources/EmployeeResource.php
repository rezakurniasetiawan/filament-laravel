<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Employee;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EmployeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use Illuminate\Support\Facades\Auth;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->required(),
                Forms\Components\TextInput::make('phone')->required(),
                Forms\Components\TextInput::make('address')->required(),
                Forms\Components\Select::make('position_id')
                    ->relationship('position', 'name')
                    // ->relationship('position', 'name', fn($query) => $query->where('id', '1'))
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active')
                    ->required(),
                // upload foto 
                Forms\Components\FileUpload::make('photo')->label('Photo')->image()->avatar()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('position.name')
                    ->label('Position')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => $record->status === 'inactive'),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn($record) => $record->status === 'inactive'),
                Tables\Actions\ViewAction::make()
                    ->hidden(fn($record) => $record->status === 'inactive'),
                Action::make('activate')
                    ->button()
                    ->action(fn($record) => static::activateCase($record))
                    ->requiresConfirmation()
                    ->color('primary')
                    ->modalHeading('Activate Employee')
                    ->modalDescription('Are you sure you\'d like to activate this employee?')
                    ->modalSubmitActionLabel('Yes, activate')
                    ->hidden(fn($record) => $record->status === 'active')
                    ->icon('heroicon-o-check-circle')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function activateCase($record)
    {
        $record->update(['status' => 'Active']);
        $recipient = Auth::user();
        Notification::make()
            ->title('Case Activated')
            ->success()
            ->body("Case is now active.")
            ->sendToDatabase($recipient);
    }

    public static function deactivateCase($record)
    {
        $record->update(['status' => 'Inactive']);
        $recipient = Auth::user();
        Notification::make()
            ->title('Case Deactivated')
            ->danger()
            ->body("Case is now inactive.")
            ->sendToDatabase($recipient);
    }

    public static function getRelations(): array
    {
        return [
            'jobdesks' => RelationManagers\JobdeskRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
