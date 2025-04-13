<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostCategoryResource\Pages;
use App\Filament\Resources\PostCategoryResource\RelationManagers\PostsRelationManager;
use App\Models\PostCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Str;

class PostCategoryResource extends Resource
{
  protected static ?string $model = PostCategory::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function getNavigationBadge(): ?string
  {
    return static::getModel()::count();
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('name')
          ->required()
          ->live(onBlur: true)
          ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

        TextInput::make('slug')
          ->required()
          ->unique(PostCategory::class, 'slug', ignoreRecord: true),

        Textarea::make('description')->nullable(),

        Toggle::make('is_visible')->label('Visible to Customers')->default(true),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')->searchable()->sortable()
        ->description(fn(PostCategory $record): string => $record->slug)->wrap(),
        IconColumn::make('is_visible')
          ->label('Visibility')
          ->boolean(),
        ToggleColumn::make('is_visible')->label('Visible'),
        TextColumn::make('updated_at')->dateTime('M d,Y')
        ->sortable()->label('Last Updated'),
        
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
          
        ]),
      ])
      ->defaultSort('name');
  }

  public static function getRelations(): array
  {
    return [
      PostsRelationManager::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListPostCategories::route('/'),
      'create' => Pages\CreatePostCategory::route('/create'),
      'edit' => Pages\EditPostCategory::route('/{record}/edit'),
    ];
  }
}
