<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostCommentResource\Pages;
use App\Filament\Resources\PostCommentResource\RelationManagers;
use App\Models\PostComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostCommentResource extends Resource
{
  protected static ?string $model = PostComment::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function getNavigationBadge(): ?string
  {
    return static::getModel()::count();
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Select::make('post_id')
          ->relationship('post', 'title')
          ->required(),

        Select::make('customer_id')
          ->relationship('customer', 'name')
          ->required(),

        Select::make('parent_id')
          ->relationship('parent', 'id')
          ->nullable()
          ->label('Reply to'),

        Textarea::make('content')->required()->rows(5),

        Toggle::make('is_visible')->default(true)->label('Visible to public'),
        Toggle::make('is_approved')->default(true)->label('Approved'),

        Select::make('approved_by')
          ->relationship('approver', 'name')
          ->nullable()
          ->label('Approved By'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('post.title')->label('Post')->searchable(),
        TextColumn::make('customer.name')->label('Customer'),
        TextColumn::make('content')->limit(50)->wrap(),
        ToggleColumn::make('is_visible')->label('Visible'),
        // IconColumn::make('is_approved')
        //   ->label('Approved')
        //   ->sortable(),
        BadgeColumn::make('is_approved')->label('Approved')->color(fn($state) => $state ? 'success' : 'danger'),
        TextColumn::make('approved_by')->formatStateUsing(fn($record) => optional($record->approver)->name),
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
      ])->defaultSort('created_at', 'desc');
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
      'index' => Pages\ListPostComments::route('/'),
      'create' => Pages\CreatePostComment::route('/create'),
      'edit' => Pages\EditPostComment::route('/{record}/edit'),
    ];
  }
}
