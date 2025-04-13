<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostTagResource\Pages;
use App\Filament\Resources\PostTagResource\RelationManagers;
use App\Models\PostCategory;
use App\Models\PostTag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostTagResource extends Resource
{
  protected static ?string $model = PostTag::class;

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
        TextColumn::make('name')->searchable()->sortable(),
        TextColumn::make('slug')->sortable(),
        ToggleColumn::make('is_visible')->label('Visible'),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ])->defaultSort('name');
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
      'index' => Pages\ListPostTags::route('/'),
      'create' => Pages\CreatePostTag::route('/create'),
      'edit' => Pages\EditPostTag::route('/{record}/edit'),
    ];
  }
}
